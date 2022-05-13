<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'rep',
        'weight',
        'created_at',
        'updated_at',
    ];

    /**
     * @param int $exercise_id
     * @param PlanExerciseDetail $plan_exercise_detail
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function createFromPlan(int $exercise_id, PlanExerciseDetail $plan_exercise_detail)
    {
        return $this->newQuery()->create(
            [
                'exercise_id' => $exercise_id,
                'rep' => $plan_exercise_detail->rep,
                'weight' => $plan_exercise_detail->weight
            ]
        );
    }

    public function deleteToTrash()
    {
        DeletedExerciseDetail::create(
            [
                'exercise_detail_id' => $this->id,
                'exercise_id' => $this->exercise_id,
                'rep' => $this->rep,
                'weight' => $this->weight
            ]
        );
        $this->delete();
    }
}
