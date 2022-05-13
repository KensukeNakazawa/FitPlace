<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanExerciseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_exercise_id',
        'rep',
        'weight'
    ];
}
