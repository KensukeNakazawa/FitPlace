<?php

namespace App\Providers;

use Domain\Repositories\Exercises\PlanExerciseDetailRepository;
use Domain\Repositories\Exercises\PlanExerciseDetailRepositoryInterface;
use Domain\Repositories\Exercises\PlanExerciseRepository;
use Domain\Repositories\Exercises\PlanExerciseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

use Domain\Repositories\Exercises\ExerciseRepository;
use Domain\Repositories\Exercises\ExerciseRepositoryInterface;

use Domain\Repositories\Exercises\ExerciseDetailRepository;
use Domain\Repositories\Exercises\ExerciseDetailRepositoryInterface;

use Domain\Repositories\Exercises\ExerciseTypeRepository;
use Domain\Repositories\Exercises\ExerciseTypeRepositoryInterface;

use App\Repositories\Notifies\LineNotifyRepository;
use Domain\Repositories\Notifies\LineNotifyRepositoryInterface;

use App\Repositories\Notifies\NotifySettingRepository;
use Domain\Repositories\Notifies\NotifySettingRepositoryInterface;

use App\Repositories\Notifies\NotifyTimeRepository;
use Domain\Repositories\Notifies\NotifyTimeRepositoryInterface;

use App\Repositories\Users\UserRepository;
use Domain\Repositories\Users\UserRepositoryInterface;



class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExerciseRepositoryInterface::class, ExerciseRepository::class);
        $this->app->bind(ExerciseDetailRepositoryInterface::class, ExerciseDetailRepository::class);
        $this->app->bind(ExerciseTypeRepositoryInterface::class, ExerciseTypeRepository::class);

        $this->app->bind(PlanExerciseRepositoryInterface::class, PlanExerciseRepository::class);
        $this->app->bind(PlanExerciseDetailRepositoryInterface::class, PlanExerciseDetailRepository::class);

        $this->app->bind(LineNotifyRepositoryInterface::class, LineNotifyRepository::class);
        $this->app->bind(NotifySettingRepositoryInterface::class, NotifySettingRepository::class);
        $this->app->bind(NotifyTimeRepositoryInterface::class, NotifyTimeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
