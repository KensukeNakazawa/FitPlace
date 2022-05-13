<?php

namespace Domain\Repositories\Exercises;

use App\Models\PlanExercise;

class PlanExerciseRepository implements PlanExerciseRepositoryInterface
{

    private PlanExercise $model;

    /**
     * PlanExerciseRepository constructor.
     * @param PlanExercise $model
     */
    public function __construct(PlanExercise $model)
    {
        $this->model = $model;
    }

    public function findById(int $plan_exercise_id)
    {
        return $this->model->newQuery()->find($plan_exercise_id);
    }


    public function create(int $user_id, int $week_day_id, int $exercise_type_id)
    {
        $last_exercise_in_day = $this->model->newQuery()
            ->where('user_id', $user_id)
            ->where('week_day_id', $week_day_id)
            ->orderBy('sort_index')
            ->get()->last();

        $sort_index = 1;
        if (!empty($last_exercise_in_day)) {
            $sort_index = $last_exercise_in_day->sort_index + 1;
        }

        return $this->model->newQuery()->create(
            [
                'user_id' => $user_id,
                'week_day_id' => $week_day_id,
                'exercise_type_id' => $exercise_type_id,
                'sort_index' => $sort_index
            ]
        );
    }


    public function getByWeekDay(int $user_id, int $week_day_id)
    {
        return $this->model->newQuery()->where(
            [
                'user_id' => $user_id,
                'week_day_id' => $week_day_id
            ]
        )->orderBy('sort_index')->get();
    }

    public function sortExercise(array $exercises)
    {
        foreach ($exercises as $exercise) {
            $this->model->newQuery()
                ->where(['id' => $exercise['exercise_id']])
                ->update(
                    [
                        'sort_index' => $exercise['update_sort_id']
                    ]
                );
        }
    }


}
