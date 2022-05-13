<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use \App\Http\Controllers\Admin\BodyPartController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\ExternalApi\LineNotifyAuthController;
use App\Http\Controllers\ExternalApi\TwitterAuthController;

Route::prefix('/socials')->group(function() {
    Route::prefix('/twitter')->group(function () {
        Route::get('/auth', [TwitterAuthController::class, 'redirectToTwitter'])->name('twitter.url');
        Route::get('/callback', [TwitterAuthController::class, 'handleCallback']);
        Route::get('/connect', [TwitterAuthController::class, 'connectTwitter']);
    });
    Route::prefix('/line_notify')->group(function () {
        Route::get('/connect', [LineNotifyAuthController::class, 'redirectToLineNotify']);
        Route::get('/callback', [LineNotifyAuthController::class, 'handleCallback']);
        Route::put('/disconnect', [LineNotifyAuthController::class, 'disconnectLineNotify']);
    });
    Route::get('/test_callback', [TwitterAuthController::class, 'testCallback']);
});

Route::prefix('/admin')->group(function() {
    Route::prefix('/auth')->group(function() {
        Route::get('/login', [LoginController::class, 'loginShow'])->name('auth.loginShow');
        Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
        Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');
    });

    Route::middleware('admin_auth')->group(function() {
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::get('/users/{user_id}', [UserController::class, 'show'])->name('user.show');

        Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercise.index');
        Route::get('/exercises/{exercise_id}', [ExerciseController::class, 'show'])->name('exercise.show');

        Route::get('/body_parts', [BodyPartController::class, 'index'])->name('body_part.index');
        Route::get('/body_parts/{body_part_id}', [BodyPartController::class, 'edit'])->name('body_part.edit');
        Route::put('/body_parts/{body_part_id}', [BodyPartController::class, 'update'])->name('body_part.update');

        Route::get('/admins/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/admins/', [AdminController::class, 'store'])->name('admin.store');
        Route::delete('/admins/{admin_id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    });

});

Route::get('/{any}', function() {
    return view('app');
})->where('any', '.*');