<?php

namespace Domain\ApplicationServices\Exercise;

use Illuminate\Support\Facades\Log;
use App\Lib\Transaction\TransactionInterface;

use App\Exceptions\ExerciseException;

use App\Models\User;
use App\Models\WeekDay;

use Domain\Repositories\Exercises\PlanExerciseRepositoryInterface;
use Domain\Repositories\Exercises\PlanExerciseDetailRepositoryInterface;

use App\Services\BaseService;

class PlanExerciseService extends BaseService
{
    private TransactionInterface $transaction;

    private WeekDay $weekDayModel;

    private PlanExerciseRepositoryInterface $planExerciseRepository;
    private PlanExerciseDetailRepositoryInterface $planExerciseDetailRepository;

    /**
     * PlanExerciseService constructor.
     * @param TransactionInterface $transaction
     * @param WeekDay $weekDayModel
     * @param PlanExerciseRepositoryInterface $planExerciseRepository
     * @param PlanExerciseDetailRepositoryInterface $planExerciseDetailRepository
     */
    public function __construct(
        TransactionInterface $transaction,
        WeekDay $weekDayModel,
        PlanExerciseRepositoryInterface $planExerciseRepository,
        PlanExerciseDetailRepositoryInterface $planExerciseDetailRepository
    ) {
        $this->transaction = $transaction;
        $this->weekDayModel = $weekDayModel;
        $this->planExerciseRepository = $planExerciseRepository;
        $this->planExerciseDetailRepository = $planExerciseDetailRepository;
    }


    /**
     * 曜日ごとのトレーニング予定を取得する
     * @param User $user
     * @return array
     */
    public function getPlanExerciseWithWeekDay(User $user)
    {
        $week_days = $this->weekDayModel->newQuery()->get();

        $result = [];

        foreach ($week_days as $week_day) {
            $exercises = $this->planExerciseRepository->getByWeekDay($user->id, $week_day->id);

            $exercise_result = [];

            foreach ($exercises as $exercise) {
                $exercise_details = $this->getExerciseDetail($exercise);
                $exercise_result[] = [
                    'exercise_id' => $exercise->id,
                    'exercise_name' => $exercise->getName(),
                    'set' => $exercise->set,
                    'exercise_details' => $exercise_details
                ];
            }

            $result[] = [
                'week_day_id' => $week_day->id,
                'week_day_name' => $week_day->name,
                'exercises' => $exercise_result
            ];
        }

        return $result;
    }

    /**
     * トレーニング予定を追加する
     * @param User $user
     * @param int $week_day_id
     * @param int $exercise_type_id
     * @param array $exercise_details
     */
    final public function addPlanExercise(
        User $user,
        int $week_day_id,
        int $exercise_type_id,
        array $exercise_details
    ) {

        $this->transaction->begin();
        try {

            $plan_exercise = $this->planExerciseRepository->create($user->id, $week_day_id, $exercise_type_id);
            $this->addPlanExerciseDetail($plan_exercise, $exercise_details);

            $this->transaction->commit();
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(500, $error->getMessage());
        }
    }

    /**
     * ユーザーの対象曜日でのトレーニング予定を取得する
     * @param User $user
     * @param int $week_day_id
     * @return array
     */
    final public function getPlanExercise(User $user, int $week_day_id)
    {
        $exercises = $this->planExerciseRepository->getByWeekDay($user->id, $week_day_id);

        $result = [];

        foreach ($exercises as $exercise) {
            $exercise_details = $this->getExerciseDetail($exercise);

            $result[] = [
                'exercise_id' => $exercise->id,
                'exercise_name' => $exercise->getName(),
                'set' => $exercise->set,
                'exercise_details' => $exercise_details,
                'sort_index' => $exercise->sort_index
            ];
        }

        return $result;
    }

    /**
     * トレーニング予定を編集するために対象のトレーニング予定を取得する
     * @param int $exercise_plan_id
     * @return array
     */
    final public function getExerciseForEdit(int $exercise_plan_id)
    {
        $exercise = $this->planExerciseRepository->findById($exercise_plan_id);
        $exercise_details = $this->getExerciseDetail($exercise);

        $result = [
            'exercise_id' => $exercise->id,
            'exercise_name' => $exercise->getName(),
            'exercise_type_id' => $exercise->exercise_type_id,
            'set' => $exercise->set,
            'exercise_details' => $exercise_details
        ];

        return $result;
    }

    /**
     * トレーニング予定を更新する
     * @param User $user
     * @param int $plan_exercise_id
     * @param array $exercise_details
     */
    final public function updatePlanExercise(User $user, int $plan_exercise_id, array $exercise_details)
    {
        $this->transaction->begin();

        try {
            $plan_exercise = $this->planExerciseRepository->findById($plan_exercise_id);

            if (!$plan_exercise->isOwned($user->id)) {
                throw new ExerciseException($this->trans_message('messages.other_user_data.update_failed'));
            }

            $this->upsertPlanExerciseDetail($plan_exercise, $exercise_details);

            $this->transaction->commit();
        } catch (ExerciseException $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(403, $error->getMessage());
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(500, $error->getMessage());
        }
    }

    final public function sortPlanExercise(int $week_day_id, array $exercises)
    {
        $this->planExerciseRepository->sortExercise($exercises);
    }

    /**
     * @param $exercise
     * @return array
     */
    final private function getExerciseDetail($exercise)
    {
        $exercise_details = [];
        foreach ($exercise->getExerciseDetail() as $exercise_detail) {
            $exercise_details[] = [
                'id' => $exercise_detail->id,
                'rep' => $exercise_detail->rep,
                'weight' => $exercise_detail->weight
            ];
        }

        return $exercise_details;
    }

    /**
     * @param $exercise
     * @param array $exercise_details
     */
    final private function upsertPlanExerciseDetail($exercise, array $exercise_details) : void
    {
        $original_exercise_details = $exercise->getExerciseDetail();

        $exercise_detail_length = count($exercise_details);
        $original_detail_length = count($original_exercise_details);

        if ($exercise_detail_length > $original_detail_length) {
            /** array shiftで一つづつ配列からエンキューするから毎回インデックスの0を指定する */
            foreach ($original_exercise_details as $original_exercise_detail) {
                $this->planExerciseDetailRepository->update($original_exercise_detail->id, $exercise_details[0]['rep'], $exercise_details[0]['weight']);
                array_shift($exercise_details);
            }
            $this->addPlanExerciseDetail($exercise, $exercise_details);
        } else {
            foreach ($original_exercise_details as $original_exercise_detail) {
                if ($exercise_details) {
                    /** array shiftで一つづつ配列からエンキューするから毎回インデックスの0を指定する */
                    $this->planExerciseDetailRepository->update($original_exercise_detail->id, $exercise_details[0]['rep'], $exercise_details[0]['weight']);
                    array_shift($exercise_details);
                } else {
                    $original_exercise_detail->delete();
                    $exercise->countDownSet();
                }
            }
        }
    }

    /**
     * @param $exercise
     * @param array $exercise_details
     */
    final private function addPlanExerciseDetail($exercise, array $exercise_details): void
    {
        foreach ($exercise_details as $exercise_detail) {
            $this->planExerciseDetailRepository->create(
                $exercise->id, $exercise_detail['rep'], $exercise_detail['weight']
            );
            $exercise->set ++;
        }
        $exercise->save();
    }
}
