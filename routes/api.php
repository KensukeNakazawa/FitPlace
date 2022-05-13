<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;

use App\Http\Controllers\Api\BodyPartController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\NotifyController;
use App\Http\Controllers\Api\PlanExerciseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WeekDayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/auth')->group(function() {
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/no_login', [LoginController::class, 'noLogin']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/authorize_code', [RegisterController::class, 'authorizeCode']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::prefix('/password_reset')->group(function () {
        Route::post('/send_mail', [PasswordResetController::class, 'sendMailForPasswordReset']);
        Route::put('/reset', [PasswordResetController::class, 'resetPassword']);
    });
});

Route::get('/week_days', [WeekDayController::class, 'index']);

// この下は認証しているユーザーのリクエストだけが通れる
Route::middleware('api_auth')->group(function() {
    Route::post('/users/store', [UserController::class, 'createUser']);

    Route::prefix('/me')->group(function() {
        Route::get('/', [MeController::class, 'me']);
        Route::get('/auth', [MeController::class, 'getMeAuth']);
        Route::get('/exercise_types', [MeController::class, 'exerciseType']);
        Route::put('/password', [MeController::class, 'changePassword']);

        Route::get('/notify_times', [NotifyController::class, 'getMeNotifyTime']);
        Route::put('/notify_times', [NotifyController::class, 'updateNotifyTime']);
        Route::get('/line_notify_access_token', [NotifyController::class, 'getLineNotify']);
        Route::put('/check_line_notify', [NotifyController::class, 'checkLineNotify']);
    });

    Route::get('/body_parts', [BodyPartController::class, 'index']);
    Route::post('/body_parts', [BodyPartController::class, 'storeExerciseType']);

    Route::get('/exercise_types/past_exercises/{exercise_type_id}', [MeController::class, 'pastExercise']);
    Route::get('/exercise_type/{exercise_type_id}', [ExerciseController::class, 'getExerciseType']);

    Route::prefix('/exercises')->group(function() {
        Route::get('/check_exist', [ExerciseController::class, 'checkExistExercise']);
        Route::get('/week_volume', [ExerciseController::class, 'getThisWeekExerciseVolume']);

        Route::put('/sort', [ExerciseController::class, 'sortExercise']);
        Route::get('/', [ExerciseController::class, 'getExercise']);
        Route::post('/', [ExerciseController::class, 'storeExercise']);

        Route::get('/{exercise_id}', [ExerciseController::class, 'edit']);
        Route::put('/{exercise_id}', [ExerciseController::class, 'updateExercise']);
        Route::delete('/{exercise_id}', [ExerciseController::class, 'deleteExercise']);

        Route::post('/add_plan_exercises', [PlanExerciseController::class, 'addPlanExercise']);
    });

    Route::prefix('exercise_plans')->group(function() {
        Route::get('/', [PlanExerciseController::class, 'index']);

        Route::get('/exercises', [PlanExerciseController::class, 'getPlanExercise']);
        Route::post('/exercises', [PlanExerciseController::class, 'store']);

        Route::get('/exercises/{exercise_plan_id}', [PlanExerciseController::class, 'edit']);
        Route::put('/exercises/{exercise_plan_id}', [PlanExerciseController::class, 'update']);

        Route::put('/exercises/sort/{week_day_id}', [PlanExerciseController::class, 'sortExercisePlan']);

        Route::delete('/{exercise_plan_id}', [PlanExerciseController::class, 'delete']);
    });
});
