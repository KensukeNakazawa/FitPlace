<?php

namespace Domain\ApplicationServices\Exercise;

use Illuminate\Database\QueryException;
use App\Exceptions\ExerciseException;

use Illuminate\Support\Facades\Log;

use App\Lib\Transaction\TransactionInterface;
use App\Lib\DateInterface;

use App\Models\User;

use Domain\Repositories\Exercises\ExerciseRepositoryInterface;
use Domain\Repositories\Exercises\ExerciseDetailRepositoryInterface;
use Domain\Repositories\Exercises\ExerciseTypeRepositoryInterface;

use App\Services\BaseService;


class ExerciseService extends BaseService
{
    private TransactionInterface $transaction;
    private DateInterface $dateInterface;

    private ExerciseRepositoryInterface $exerciseRepository;
    private ExerciseDetailRepositoryInterface $exerciseDetailRepository;
    private ExerciseTypeRepositoryInterface $exerciseTypeRepository;

    /**
     * ExerciseService constructor.
     * @param TransactionInterface $transaction
     * @param DateInterface $dateInterface
     * @param ExerciseRepositoryInterface $exerciseRepository
     * @param ExerciseDetailRepositoryInterface $exerciseDetailRepository
     * @param ExerciseTypeRepositoryInterface $exerciseTypeRepository
     */
    public function __construct(
        TransactionInterface $transaction,
        DateInterface $dateInterface,
        ExerciseRepositoryInterface $exerciseRepository,
        ExerciseDetailRepositoryInterface $exerciseDetailRepository,
        ExerciseTypeRepositoryInterface $exerciseTypeRepository
    ) {
        $this->transaction = $transaction;
        $this->dateInterface = $dateInterface;
        $this->exerciseRepository = $exerciseRepository;
        $this->exerciseDetailRepository = $exerciseDetailRepository;
        $this->exerciseTypeRepository = $exerciseTypeRepository;
    }


    /**
     * トレーニングを追加する
     * @param User $user
     * @param int $exercise_type_id
     * @param array $exercise_details
     */
    final public function addExercise(User $user, int $exercise_type_id, array $exercise_details, $date = null) : void
    {
        $this->transaction->begin();
        try {

            $exercise = $this->exerciseRepository->create($user->id, $exercise_type_id, $date);
            $this->addExerciseDetail($exercise, $exercise_details);

            $this->transaction->commit();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            $this->transaction->rollBack();
            abort(500, $this->trans_message('messages.register_failed'));
        }
    }

    /**
     * 対象のトレーニングを更新する
     * @param User $user
     * @param int $exercise_id
     * @param array $exercise_details
     */
    final public function updateExercise(User $user, int $exercise_id, array $exercise_details) : void
    {
        $this->transaction->begin();
        try {

            $exercise = $this->exerciseRepository->findById($exercise_id);

            if (!$exercise->isOwned($user->id)) {
                throw new ExerciseException($this->trans_message('messages.other_user_data.update_failed'));
            }

            $this->upsertExerciseDetail($exercise, $exercise_details);

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

    final public function sortExercise(array $exercises)
    {
        $this->exerciseRepository->sortExercise($exercises);
    }

    /**
     * 対象の日付でのトレーニング一覧を取得する
     * @param User $user
     * @param $date
     * @return array
     */
    final public function getExerciseForIndex(User $user, $date = null)
    {
        $exercises = $this->exerciseRepository->getByDate($user->id, $date);

        $result = [];

        foreach ($exercises as $exercise) {

            $exercise_details = $this->getExerciseDetail($exercise);

            $result[] = [
                'exercise_id' => $exercise->id,
                'exercise_type_id' => $exercise->exercise_type_id,
                'exercise_name' => $exercise->getName(),
                'exercise_max_weight' => $exercise->getMaxWeight(),
                'set' => $exercise->set,
                'exercise_details' => $exercise_details
            ];
        }

        return $result;
    }

    /**
     * 編集のために対象のトレーニングを取得する
     * @param $exercise_id
     * @return array $result
     */
    final public function getExerciseForEdit(int $exercise_id) : array
    {
        $exercise = $this->exerciseRepository->findById($exercise_id);
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
     * 対象のトレーニング種目を取得する
     * @param int $exercise_type_id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    final public function getExerciseType(int $exercise_type_id)
    {
        return $this->exerciseTypeRepository->findById($exercise_type_id);
    }

    /**
     * 対象の日付、トレーニング種目でのトレーニングを取得する、なければnull
     * @param User $user
     * @param int $exercise_type_id
     * @param null $date
     * @return mixed
     */
    final public function getExercise(User $user, int $exercise_type_id, $date = null)
    {
        return $this->exerciseRepository->findWithTargetDay($user->id, $exercise_type_id, $date);
    }

    /**
     * 今週のトレーニングボリュームを取得
     * @param User $user
     * @return mixed
     */
    final public function getThisWeekExerciseVolume(User $user)
    {
        $start_date = $this->dateInterface->getStartOfWeek();
        $end_date = $this->dateInterface->getEndOfWeek();

        return $this->exerciseRepository->getTargetPeriodExerciseVolume($user->id, $start_date, $end_date);
    }


    /**
     * トレーニング詳細を追加する
     * @param $exercise
     * @param array $exercise_details
     */
    final private function addExerciseDetail($exercise, array $exercise_details) : void
    {
        $exercise_type = $this->exerciseTypeRepository->findById($exercise->exercise_type_id);

        foreach ($exercise_details as $exercise_detail) {
            $exercise_detail_model = $this->exerciseDetailRepository->create($exercise->id, $exercise_detail['rep'], $exercise_detail['weight']);

            $exercise->volume += $exercise_detail_model->weight * $exercise_detail_model->rep;
            $exercise_detail_max = $this->calculateMaxWeight( $exercise_detail_model->weight, $exercise_detail_model->rep);

            if ($exercise->max < $exercise_detail_max) {
                $exercise->max = $exercise_detail_max;
            }

            $exercise_type->updateMaxWeight($exercise->max);

            $exercise->set ++;
        }

        $exercise->save();
    }


    /**
     * 対象のトレーニングの詳細を取得する
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
     * トレーニング詳細を更新、無ければ追加する
     * 元のトレーニング詳細が更新するトレーニング種目より小さい時はトレーニング詳細を追加する
     * 大きい時は、トレーニング嘱目を更新し、余ったものを削除する
     * @param $exercise
     * @param array $exercise_details
     */
    final private function upsertExerciseDetail($exercise, array $exercise_details) : void
    {

        $original_exercise_details = $exercise->getExerciseDetail();

        $exercise_detail_length = count($exercise_details);
        $original_detail_length = count($original_exercise_details);

        $exercise_type = $this->exerciseTypeRepository->findById($exercise->exercise_type_id);

        if ($exercise_detail_length > $original_detail_length) {

            $max = 0;
            $volume = 0;
            $set = 0;

            /**
             * 数が重複している部分は更新する
             */
            foreach ($original_exercise_details as $original_exercise_detail) {
                /** array shiftで一つづつ配列からエンキューするから毎回インデックスの0を指定する */
                $exercise_detail_model = $this->exerciseDetailRepository->update($original_exercise_detail->id, $exercise_details[0]['rep'], $exercise_details[0]['weight']);

                $volume += $exercise_detail_model->weight * $exercise_detail_model->rep;

                $exercise_detail_max = $this->calculateMaxWeight($exercise_detail_model->weight, $exercise_detail_model->rep);
                if ($max < $exercise_detail_max) {
                    $max = $exercise_detail_max;
                }
                $exercise_type->updateMaxWeight($max);

                array_shift($exercise_details);

                $set ++;
            }

            $exercise->update(
                [
                    'volume' => $volume,
                    'max' => $max,
                    'set' => $set
                ]
            );

            /** 追加する分のトレーニング詳細を追加 */
            $this->addExerciseDetail($exercise, $exercise_details);

        } else {
            $max = 0;
            $volume = 0;
            $set = 0;

            foreach ($original_exercise_details as $original_exercise_detail) {
                /** array shiftで一つづつ配列からエンキューするから毎回インデックスの0を指定する */
                if ($exercise_details) {
                    $exercise_detail_model = $this->exerciseDetailRepository->update($original_exercise_detail->id, $exercise_details[0]['rep'], $exercise_details[0]['weight']);

                    $volume += $exercise_detail_model->weight * $exercise_detail_model->rep;

                    $exercise_detail_max = $this->calculateMaxWeight($exercise_detail_model->weight, $exercise_detail_model->rep);
                    if ($max < $exercise_detail_max) {
                        $max = $exercise_detail_max;
                    }
                    $exercise_type->updateMaxWeight($max);

                    array_shift($exercise_details);

                    $set ++;
                } else {
                    $original_exercise_detail->deleteToTrash();
                }
            }

            $exercise->update(
                [
                    'max' => $max,
                    'volume' => $volume,
                    'set' => $set
                ]
            );
        }
    }

    /**
     * 最大重量を計算する
     * @param float $weight
     * @param int $rep
     * @return float|int
     */
    final private function calculateMaxWeight(float $weight, int $rep) : float
    {
        return round(($weight * $rep) / 40 + $weight, 2);
    }
}
