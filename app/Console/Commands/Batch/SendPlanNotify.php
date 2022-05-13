<?php

namespace App\Console\Commands\Batch;

use Illuminate\Console\Command;

use Domain\ApplicationServices\Batch\SendPlanNotifyService;


class SendPlanNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:send_plan_notify {time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch: Send plan notify to user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SendPlanNotifyService $sendPlanNotifyService)
    {
        $time = $this->argument('time');
        $sendPlanNotifyService->SendNotifyAtHour($time);
        return 0;
    }
}
