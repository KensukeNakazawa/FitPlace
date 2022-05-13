<?php

namespace Tests\Unit\Domain\ApplicationServices\Notify;

use App\Models\Auth;
use App\Models\User;
use App\Models\LineNotify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LineNotifyServiceTest extends TestCase
{

    use RefreshDatabase;

    protected $app;
    private $lineNotifyService;

    public function testSetAccessToken()
    {
        $this->lineNotifyService = $this->app->make('Domain\ApplicationServices\Notify\LineNotifyService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        /** 作られるかどうか */
        $this->lineNotifyService->setAccessToken($user, 'test');
        $line_notify = LineNotify::where('user_id', $user->id)->first();
        $this->assertNotEmpty($line_notify);
        $this->assertEquals('test', $line_notify->access_token);

        /** 更新されるかどうか */
        $this->lineNotifyService->setAccessToken($user, 'test_1');
        $line_notify = LineNotify::where('user_id', $user->id)->first();
        $this->assertEquals('test_1', $line_notify->access_token);

    }

    public function testGetLineNotifySuccess()
    {
        $this->lineNotifyService = $this->app->make('Domain\ApplicationServices\Notify\LineNotifyService');
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $notify_access_token = 'testGetLineNotifySuccessAccessToken';
        LineNotify::factory()->create(['user_id' => $user->id, 'access_token' => $notify_access_token]);

        $line_notify = $this->lineNotifyService->getLineNotify($user);

        $this->assertNotEmpty($line_notify);
        $this->assertNotEmpty($line_notify->access_token);
    }

    public function testGetLineNotifyCheckAtSuccess()
    {
        $this->lineNotifyService = $this->app->make('Domain\ApplicationServices\Notify\LineNotifyService');
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        LineNotify::factory()->create(['user_id' => $user->id, 'check_at' => date('Y-m-d H:i:s')]);

        $line_notify = $this->lineNotifyService->getLineNotify($user);

        $this->assertNotEmpty($line_notify);
        $this->assertEmpty($line_notify->access_token);
        $this->assertNotEmpty($line_notify->check_at);
    }

    public function testCheckLineNotify()
    {
        $this->lineNotifyService = $this->app->make('Domain\ApplicationServices\Notify\LineNotifyService');
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $this->lineNotifyService->checkLineNotify($user);

        $line_notify = LineNotify::where('user_id', $user->id)->get()->first();
        $this->assertNotEmpty($line_notify);
        $this->assertEmpty($line_notify->access_token);
    }
}