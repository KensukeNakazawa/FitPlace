<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedExerciseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_detail_id',
        'exercise_id',
        'rep',
        'weight',
        'created_at',
        'updated_at'
    ];
}
