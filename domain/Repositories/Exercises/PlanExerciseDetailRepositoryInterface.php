<?php

namespace Domain\Repositories\Exercises;

interface PlanExerciseDetailRepositoryInterface
{
    /**
     * @param int $plan_exercise_id
     * @param int|null $rep
     * @param float|null $weight
     * @return mixed
     */
    public function create(int $plan_exercise_id, int $rep=null, float $weight=null);

    /**
     * @param int $plan_exercise_detail_id
     * @param int|null $rep
     * @param float|null $weight
     * @return mixed
     */
    public function update(int $plan_exercise_detail_id, int $rep=null, float $weight=null);

}
