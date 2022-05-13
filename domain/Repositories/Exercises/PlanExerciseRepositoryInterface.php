<?php

namespace Domain\Repositories\Exercises;

interface PlanExerciseRepositoryInterface
{

    public function findById(int $plan_exercise_id);

    public function create(int $user_id, int $week_day_id, int $exercise_type_id);

    public function getByWeekDay(int $user_id, int $week_day_id);

    public function sortExercise(array $exercises);

}
