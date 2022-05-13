<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('batch:send_plan_notify 6')->dailyAt('6:00');
        $schedule->command('batch:send_plan_notify 12')->dailyAt('12:00');
        $schedule->command('batch:send_plan_notify 18')->dailyAt('18:00');

        // 毎週日曜日の午前1時に実行
        $schedule->exec('python3 batch/weekly_batch.py')->weeklyOn(0, '1:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
