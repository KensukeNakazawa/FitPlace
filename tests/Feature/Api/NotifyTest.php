<?php

namespace Tests\Feature\Api;

use App\Models\Auth;
use App\Models\LineNotify;
use App\Models\NotifySetting;
use App\Models\NotifyTime;
use App\Models\User;

class NotifyTest extends BaseTestCase
{
    public function testGetMeNotifyTime()
    {
        $email = 'testMeSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $notify_time = NotifyTime::factory()->create(['time' => 6]);
        $notify_setting = NotifySetting::factory()->create(['user_id' => $user->id, 'notify_time_id' => $notify_time->id]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/me/notify_times');
        $response->assertOk();
        $this->assertEquals($notify_setting->id, $response->json(['notify_settings'])[0]['id']);
    }

    public function testUpdateNotifyTimeSuccess()
    {
        $email = 'testUpdateNotifyTimeSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $times = [6, 12, 18];
        $notify_setting_ids = [];
        for ($i=0; $i<3; $i++) {
            $notify_time = NotifyTime::factory()->create(['time' => $times[$i]]);
            $notify_setting = NotifySetting::factory()->create(['user_id' => $user->id, 'notify_time_id' => $notify_time->id]);
            $notify_setting_ids[] = $notify_setting->id;
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/notify_times', [
                'notify_setting_ids' => [$notify_setting_ids[0], $notify_setting_ids[1]]
            ]);
        $response->assertOk();
        $updated_notify_setting = NotifySetting::where('user_id', $user->id)->get();
        $this->assertEquals(2, $updated_notify_setting->count());
    }

    public function testGetLineNotifySuccess()
    {
        $email = 'testGetLineNotifySuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $notify_access_token = 'testGetLineNotifySuccessAccessToken';
        $date_time = date('Y-m-d H:i:s');
        LineNotify::factory()->create(['user_id' => $user->id, 'access_token' => $notify_access_token, 'check_at' => $date_time]);
        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/me/line_notify_access_token');
        $response->assertOk();
        $this->assertEquals($notify_access_token, $response->json(['line_notify'])['access_token']);
        $this->assertEquals($date_time, $response->json(['line_notify'])['check_at']);
    }

    public function testCheckLineNotifySuccessNotExist()
    {
        $email = 'testCheckLineNotifySuccessNotExist@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/check_line_notify');
        $response->assertOk();

        $line_notify = LineNotify::where('user_id', $user->id)->get()->first();
        $this->assertNotEmpty($line_notify->check_at);
        $this->assertEmpty($line_notify->access_token);
        $this->assertNotEmpty($line_notify);
    }

    public function testCheckLineNotifySuccessAlreadyExist()
    {
        $email = 'testCheckLineNotifySuccessAlreadyExist@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $notify_access_token = 'testGetLineNotifySuccessAccessToken';
        LineNotify::factory()->create(['user_id' => $user->id, 'access_token' => 'testGetLineNotifySuccessAccessToken']);
        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/check_line_notify');
        $response->assertOk();

        $line_notify = LineNotify::where('user_id', $user->id)->get()->first();
        $this->assertNotEmpty($line_notify->check_at);
        $this->assertEquals($notify_access_token, $line_notify->access_token);
    }

}