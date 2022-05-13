<?php

namespace Domain\ApplicationServices\Exercise;

use Illuminate\Support\Facades\Log;
use App\Lib\Transaction\TransactionInterface;

use App\Models\Exercise;
use App\Models\ExerciseType;
use App\Models\ExerciseDetail;
use App\Models\PlanExercise;
use App\Models\PlanExerciseDetail;
use App\Models\User;

use Domain\Repositories\Exercises\PlanExerciseRepositoryInterface;
use Illuminate\Database\QueryException;

use App\Services\BaseService;

class PlanExerciseRegisterService extends BaseService
{
    private TransactionInterface $transaction;

    private Exercise $exerciseModel;
    private ExerciseType $exerciseTypeModel;
    private ExerciseDetail $exerciseDetailModel;
    private PlanExercise $planExerciseModel;
    private PlanExerciseDetail $planExerciseDetailModel;

    private PlanExerciseRepositoryInterface $planExerciseRepository;

    /**
     * PlanExerciseRegisterService constructor.
     * @param TransactionInterface $transaction
     * @param Exercise $exerciseModel
     * @param ExerciseType $exerciseTypeModel
     * @param ExerciseDetail $exerciseDetailModel
     * @param PlanExercise $planExerciseModel
     * @param PlanExerciseDetail $planExerciseDetailModel
     * @param PlanExerciseRepositoryInterface $planExerciseRepository
     */
    public function __construct(
        TransactionInterface $transaction,
        Exercise $exerciseModel,
        ExerciseType $exerciseTypeModel,
        ExerciseDetail $exerciseDetailModel,
        PlanExercise $planExerciseModel,
        PlanExerciseDetail $planExerciseDetailModel,
        PlanExerciseRepositoryInterface $planExerciseRepository
    ) {
        $this->transaction = $transaction;
        $this->exerciseModel = $exerciseModel;
        $this->exerciseTypeModel = $exerciseTypeModel;
        $this->exerciseDetailModel = $exerciseDetailModel;
        $this->planExerciseModel = $planExerciseModel;
        $this->planExerciseDetailModel = $planExerciseDetailModel;
        $this->planExerciseRepository = $planExerciseRepository;
    }


    /**
     * 対象のユーザーと曜日の予定の筋トレを登録する
     * @param User $user
     * @param int $week_day_id
     */
    public function register(User $user, int $week_day_id) : void
    {
        $plan_exercises = $this->planExerciseRepository->getByWeekDay($user->id, $week_day_id);

        $this->transaction->begin();
        try {

            $this->copyExerciseFromPlan($plan_exercises);

            $this->transaction->commit();
        } catch (QueryException $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(500, $this->trans_message('messages.register_failed'));
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(500, $error->getMessage());
        }
    }

    /**
     * 予定の筋トレから新しく生成する
     * @param $plan_exercises
     */
    final private function copyExerciseFromPlan($plan_exercises) : void
    {
        foreach ($plan_exercises as $plan_exercise) {
            $plan_exercise_details = $plan_exercise->getExerciseDetail();

            $exercise = $this->exerciseModel->createFromPlan($plan_exercise);
            $exercise_type = $this->exerciseTypeModel->newQuery()->find($exercise->exercise_type_id);

            $max = 0;
            $volume = 0;
            foreach ($plan_exercise_details as $plan_exercise_detail) {
                $exercise_detail = $this->exerciseDetailModel->createFromPlan($exercise->id, $plan_exercise_detail);

                $volume += $exercise_detail->weight * $exercise_detail->rep;

                /** TODO: ここの計算をまとめる, ExerciseServiceにもある */
                $exercise_detail_max = round(($exercise_detail->weight * $exercise_detail->rep) / 40 + $exercise_detail->weight, 2);

                if ($max < $exercise_detail_max) {
                    $max = $exercise_detail_max;
                }

                $exercise_type->updateMaxWeight($max);
            }

            $exercise->update(
                [
                    'max' => $max,
                    'volume' => $volume
                ]
            );
        }
    }

}
