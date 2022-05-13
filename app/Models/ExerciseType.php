<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Pagination\LengthAwarePaginator;

class ExerciseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'body_part_id',
        'user_id',
        'name',
        'max_weight',
        'sort_index',
        'created_at',
        'updated_at'
    ];

    public function getExerciseType(int $user_id)
    {
        return $this->newQuery()->where(['user_id' => $user_id])
            ->orderBy('sort_index')
            ->get();
    }

    /**
     * @param float $max
     */
    public function updateMaxWeight(float $max = null) : void
    {
        if ($max === null) {
            return;
        }
        if ($this->max_weight < $max) {
            $this->update(['max_weight' => $max]);
        }
    }

    /**
     * @param int $exercise_type_id
     * @param int $page
     * @return mixed
     */
    public function pastExercise(int $exercise_type_id, int $page = 1)
    {
        return Exercise::where('exercise_type_id', $exercise_type_id)
            ->orderBy('exercise_at', 'DESC')
            ->forPage($page, 15)->get();
    }

    public function exercises()
    {
        $this->hasMany('App/Models/Exercise');
    }
}
