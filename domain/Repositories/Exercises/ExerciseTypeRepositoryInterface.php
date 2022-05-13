<?php

namespace Domain\Repositories\Exercises;

interface ExerciseTypeRepositoryInterface
{
    public function findById(int $exercise_type_id);
}