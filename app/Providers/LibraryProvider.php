<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Lib\ArrayImplement;
use Domain\Lib\ArrayInterface;

use App\Lib\Cache\RedisInterface;
use App\Lib\Cache\RedisImplement;

use App\Lib\Transaction\TransactionInterface;
use App\Lib\Transaction\DBTransaction;

use App\Lib\DateInterface;
use App\Lib\DateImplement;


class LibraryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ArrayInterface::class, ArrayImplement::class);
        $this->app->bind(RedisInterface::class, RedisImplement::class);
        $this->app->bind(TransactionInterface::class, DBTransaction::class);
        $this->app->bind(DateInterface::class, DateImplement::class);
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
