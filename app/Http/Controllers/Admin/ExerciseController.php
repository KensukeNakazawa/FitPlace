<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::paginate(config('constants.PAGINATION'));

        return view('exercise.index', ['exercises' => $exercises]);
    }

    public function show($exercise_id)
    {
        $exercise = Exercise::find($exercise_id);

        return view('exercise.show', ['exercise' => $exercise]);
    }

}
