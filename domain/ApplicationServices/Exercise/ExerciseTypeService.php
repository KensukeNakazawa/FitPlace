<?php

namespace Domain\ApplicationServices\Exercise;

use Illuminate\Support\Facades\Log;
use App\Lib\Cache\RedisInterface;

use App\Exceptions\ExerciseException;

use App\Models\BodyPart;
use App\Models\ExerciseType;
use App\Models\User;

use App\Services\BaseService;

class ExerciseTypeService extends BaseService
{
    private RedisInterface $redis;

    private BodyPart $bodyPartModel;
    private ExerciseType $exerciseTypeModel;

    /**
     * ExerciseTypeService constructor.
     * @param RedisInterface $redis
     * @param BodyPart $bodyPartModel
     * @param ExerciseType $exerciseTypeModel
     */
    public function __construct(RedisInterface $redis, BodyPart $bodyPartModel, ExerciseType $exerciseTypeModel)
    {
        $this->redis = $redis;
        $this->bodyPartModel = $bodyPartModel;
        $this->exerciseTypeModel = $exerciseTypeModel;
    }


    /**
     * 対象のユーザーのトレーニング種目を取得する
     * @param int $user_id
     * @return array
     */
    final public function getExerciseType(int $user_id)
    {
        $body_parts = $this->bodyPartModel->newQuery()->get();
        $exercise_types =  $this->exerciseTypeModel->getExerciseType($user_id);

        $result = [];

        foreach ($body_parts as $body_part) {
            $target_exercise_types = [];
            foreach ($exercise_types as $exercise_type) {
                if ($body_part->id == $exercise_type->body_part_id) {
                    $target_exercise_types[] = [
                        'exercise_type_id' => $exercise_type->id,
                        'exercise_type_name' => $exercise_type->name
                    ];
                }
            }
            $result[] = [
                'body_part_id' => $body_part->id,
                'body_part_name' => $body_part->name,
                'exercise_types' => $target_exercise_types
            ];
        }
        return $result;
    }

    /**
     * トレーニングの種目を追加する
     * @param User $user
     * @param int $body_part_id
     * @param string $exercise_type_name
     */
    final public function addExerciseType(User $user, int $body_part_id, string $exercise_type_name)
    {
        /** 現在の対象部位のトレーニング種目を取得 */
        $original_exercise_types = $this->exerciseTypeModel->newQuery()->where(
            [
                'user_id' => $user->id,
                'body_part_id' => $body_part_id
            ]
        )->orderBy('sort_index', 'desc')->get();

        try {
            $sort_index = 1;
            foreach ($original_exercise_types as $original_exercise_type) {
                if ($original_exercise_type->name === $exercise_type_name) {
                    throw new ExerciseException($this->trans_message('messages.exercise_type.duplicate'));
                }
                $sort_index += 1;
            }
            $exercise_type = ExerciseType::create(
                [
                    'body_part_id' => $body_part_id,
                    'user_id' => $user->id,
                    'name' => $exercise_type_name,
                    'sort_index' => $sort_index
                ]
            );
        } catch (ExerciseException $error) {
            Log::error($error->getMessage());
            abort(500, $error->getMessage());
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            abort(500, $this->trans_message('messages.register_failed'));
        }
        return $exercise_type;
    }


    /**
     * 対象のユーザーの過去のトレーニングを取得する.
     * @param User $user
     * @param int $exercise_type_id
     * @param int $page
     * @return array
     */
    final public function getPastExercise(User $user, int $exercise_type_id, int $page = 1)
    {
        $exercise_type = $this->exerciseTypeModel->newQuery()->find($exercise_type_id);
        $exercises = $this->exerciseTypeModel->pastExercise($exercise_type_id, $page);

        try {
            if ($exercises->isNotEmpty() && !$exercises->first()->isOwned($user->id)) {
                throw new ExerciseException($this->trans_message('messages.other_user_data.get_failed'));
            }
        } catch (ExerciseException $error) {
            Log::error($error->getMessage());
            abort(401, $error->getMessage());
        }

        $result_exercises = [];
        foreach ($exercises as $exercise) {
            $result_exercise = $exercise;
            $result_exercise['exercise_details'] = $exercise->getExerciseDetail();
            $result_exercises[]= $result_exercise;
        }

        $result = [
            'exercise_type' => $exercise_type,
            'exercises' => $result_exercises
        ];

        return $result;
    }
}