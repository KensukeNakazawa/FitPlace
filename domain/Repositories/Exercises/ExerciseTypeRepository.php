<?php

namespace Domain\Repositories\Exercises;

use App\Models\ExerciseType;

class ExerciseTypeRepository implements ExerciseTypeRepositoryInterface
{
    private ExerciseType $model;

    /**
     * ExerciseTypeRepository constructor.
     * @param ExerciseType $model
     */
    public function __construct(ExerciseType $model)
    {
        $this->model = $model;
    }

    public function findById(int $exercise_type_id)
    {
        return $this->model->newQuery()->find($exercise_type_id);
    }


}