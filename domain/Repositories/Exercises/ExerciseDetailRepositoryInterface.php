<?php

namespace Domain\Repositories\Exercises;

interface ExerciseDetailRepositoryInterface
{
    /**
     * @param int $exercise_id
     * @param int|null $rep
     * @param float|null $weight
     * @return mixed
     */
    public function create(int $exercise_id, int $rep=null, float $weight=null);

    /**
     * @param int $exercise_detail_id
     * @param int|null $rep
     * @param float|null $weight
     * @return mixed
     */
    public function update(int $exercise_detail_id, int $rep=null, float $weight=null);
}