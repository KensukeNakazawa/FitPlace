<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_type_id',
        'set',
        'sort_index',
        'volume',
        'max',
        'exercise_at',
        'created_at',
        'updated_at'
    ];


    protected $dates = [
        'exercise_at'
    ];

    /**
     * 予定していたトレーニングから新たにトレーニングを追加する
     * @param PlanExercise $plan_exercise
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function createFromPlan(PlanExercise $plan_exercise)
    {
        return $this->newQuery()->create(
            [
                'user_id' => $plan_exercise->user_id,
                'exercise_type_id' => $plan_exercise->exercise_type_id,
                'set' => $plan_exercise->set,
                'sort_index' => $plan_exercise->sort_index,
                'exercise_at' => now()
            ]
        );
    }

    public function getName() : string
    {
        return ExerciseType::find($this->exercise_type_id)->name;
    }

    /**
     * @return float
     */
    public function getMaxWeight() : float
    {
        return ExerciseType::find($this->exercise_type_id)->max_weight;
    }


    // ここからは関連付け

    /**
     * @return ExerciseDetail
     */
    public function getExerciseDetail()
    {
        return ExerciseDetail::where(['exercise_id' => $this->id])->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exerciseType()
    {
        return $this->belongsTo('App\Models\ExerciseType');
    }


    // この下はモデルに対するメソッドを記述

    public function countUpSet(): void
    {
        $this->newQuery()->update(['set' => $this->set + 1]);
    }

    public function countDownSet(): void
    {
        $this->newQuery()->update(['set' => $this->set - 1]);
    }


    public function isOwned(int $user_id) : bool
    {
        return $this->user_id === $user_id;
    }

    public function deleteAll() : void
    {
        $exercise_details = ExerciseDetail::where('exercise_id', $this->id)->get();

        $deleted_exercise = DeletedExercise::create(
            [
                'exercise_id' => $this->id,
                'user_id' => $this->user_id,
                'exercise_type_id' => $this->exercise_type_id,
                'set' => $this->set,
                'volume' => $this->volume,
                'max' => $this->max,
                'exercise_at' => $this->exercise_at
            ]
        );

        foreach ($exercise_details as $exercise_detail) {
            $exercise_detail->deleteToTrash();
        }

        $this->delete();
    }


}
