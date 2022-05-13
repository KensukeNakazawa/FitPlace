<?php

namespace Tests\Unit\Domain\ApplicationServices\Notify;

use App\Models\Auth;
use App\Models\User;
use App\Models\NotifyTime;
use App\Models\NotifySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifyServiceTest extends TestCase
{

    use RefreshDatabase;

    protected $app;
    private $notifyService;

    public function testUpdateOrCreateNotifySetting()
    {
        $this->lineNotifyService = $this->app->make('Domain\ApplicationServices\Notify\NotifyService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $factory_notify_times = [6, 12, 18];
        foreach ($factory_notify_times as $factory_notify_time) {
            NotifyTime::factory()->create(['time' => $factory_notify_time]);
        }

        /** 元の数より増える場合 */
        NotifySetting::factory()->create(['user_id' => $user->id, 'notify_time_id' => 1]);
        $notify_settings_ids = [1, 2];
        $this->lineNotifyService->updateOrCreateNotifySetting($user, $notify_settings_ids);
        $notify_settings = NotifySetting::where('user_id', $user->id)->get();
        $this->assertEquals(count($notify_settings_ids), $notify_settings->count());

        /** 元の数と変わらないけど、内容が変わる場合 */
        $notify_settings_ids = [2];
        $this->lineNotifyService->updateOrCreateNotifySetting($user, $notify_settings_ids);
        $notify_settings = NotifySetting::where('user_id', $user->id)->get();
        $this->assertEquals(count($notify_settings_ids), $notify_settings->count());
        $this->assertEquals($notify_settings->first()->notify_time_id, 2);

        /** 元の数より減る場合 */
        NotifySetting::factory()->create(['user_id' => $user->id, 'notify_time_id' => 1]);
        NotifySetting::factory()->create(['user_id' => $user->id, 'notify_time_id' => 3]);
        $notify_settings_ids = [2];
        $this->lineNotifyService->updateOrCreateNotifySetting($user, $notify_settings_ids);
        $notify_settings = NotifySetting::where('user_id', $user->id)->get();
        $this->assertEquals(count($notify_settings_ids), $notify_settings->count());
    }

}