<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_type_id',
        'week_day_id',
        'set',
        'sort_index'
    ];

    public function getName()
    {
        return ExerciseType::find($this->exercise_type_id)->name;
    }

    /**
     * @return ExerciseDetail
     */
    public function getExerciseDetail()
    {
        return PlanExerciseDetail::where(['plan_exercise_id' => $this->id])->get();
    }

    // この下はモデルに対するメソッドを記述

    public function isOwned(int $user_id) : bool
    {
        return $user_id === $this->user_id;
    }


    public function countUpSet(): void
    {
        $this->newQuery()->update(['set' => $this->set + 1]);
    }

    public function countDownSet(): void
    {
        $this->newQuery()->update(['set' => $this->set - 1]);
    }

    /**
     * @throws \Exception
     */
    public function deleteAll() : void
    {
        PlanExerciseDetail::where('plan_exercise_id', $this->id)->delete();
        $this->delete();
    }
}
