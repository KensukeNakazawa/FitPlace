<?php

namespace App\Services\Exercise;

use Illuminate\Support\Facades\Log;

use App\Exceptions\ExerciseException;

use App\Lib\Transaction\TransactionInterface;

use App\Models\Exercise;
use App\Models\PlanExercise;
use App\Models\User;

use App\Services\BaseService;

class DeleteService extends BaseService
{
    private TransactionInterface $transaction;

    private Exercise $exerciseModel;
    private PlanExercise $planExerciseModel;

    /**
     * DeleteService constructor.
     * @param TransactionInterface $transaction
     * @param Exercise $exerciseModel
     * @param PlanExercise $planExerciseModel
     */
    public function __construct(
        TransactionInterface $transaction,
        Exercise $exerciseModel,
        PlanExercise $planExerciseModel
    ) {
        $this->transaction = $transaction;
        $this->exerciseModel = $exerciseModel;
        $this->planExerciseModel = $planExerciseModel;
    }


    /**
     * 対象のトレーニングを削除する
     * @param User $user
     * @param int $exercise_id
     */
    final public function deleteExercise(User $user, int $exercise_id) : void
    {
        $exercise = $this->exerciseModel->newQuery()->find($exercise_id);

        $this->transaction->begin();
        try {
            if (!$exercise->isOwned($user->id)) {
                throw new ExerciseException($this->trans_message('messages.other_user_data.delete_failed'));
            }

            $exercise->deleteAll();

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

    /**
     * 対象のトレーニング予定を削除する
     * @param User $user
     * @param int $plan_exercise_id
     */
    final public function deletePlanExercise(User $user, int $plan_exercise_id) : void
    {
        $plan_exercise = $this->planExerciseModel->newQuery()->find($plan_exercise_id);

        $this->transaction->begin();
        try {

            if (!$plan_exercise->isOwned($user->id)) {
                throw new ExerciseException($this->trans_message('messages.other_user_data.delete_failed'));
            }

            $plan_exercise->deleteAll();

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
}
