<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'user_id',
        'exercise_type_id',
        'set',
        'volume',
        'max',
        'exercise_at',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'exercise_at'
    ];
}
