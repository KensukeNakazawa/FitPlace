<?php

namespace Domain\Repositories\Exercises;

use App\Models\PlanExerciseDetail;

class PlanExerciseDetailRepository implements PlanExerciseDetailRepositoryInterface
{
    private PlanExerciseDetail $model;

    /**
     * PlanExerciseDetailRepository constructor.
     * @param PlanExerciseDetail $model
     */
    public function __construct(PlanExerciseDetail $model)
    {
        $this->model = $model;
    }


    public function create(int $plan_exercise_id, int $rep=null, float $weight=null)
    {
        if ($rep === null) {
            $rep = 0;
        }
        if ($weight === null) {
            $weight = 0;
        }

        return $this->model->newQuery()->create(
            [
                'plan_exercise_id' => $plan_exercise_id,
                'rep' => $rep,
                'weight' => $weight
            ]
        );
    }

    public function update(int $plan_exercise_detail_id, int $rep = null, float $weight = null)
    {
        if ($rep === null) {
            $rep = 0;
        }
        if ($weight === null) {
            $weight = 0;
        }

        $exercise_detail = $this->model->newQuery()->find($plan_exercise_detail_id);

        $exercise_detail->rep = $rep;
        $exercise_detail->weight = $weight;
        $exercise_detail->save();
        return $exercise_detail;
    }
}
