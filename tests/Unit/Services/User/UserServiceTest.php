<?php

namespace Tests\Unit\Services\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

use App\Models\Auth;
use App\Models\User;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $app;
    private $userService;

    public function testGetUser()
    {
        $this->userService = $this->app->make('App\Services\User\UserService');
        $name = 'kensuke_test';
        $birth_day =  new Carbon('1999-01-19');
        $auth = Auth::factory()->create(['email' => 'test@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $get_user = $this->userService->getUser($user->id);

        $this->assertEquals($user->id, $get_user->id);
    }

    public function testGetUserByAuthId()
    {
        $this->userService = $this->app->make('App\Services\User\UserService');
        $name = 'kensuke_test';
        $birth_day =  new Carbon('1999-01-19');
        $auth = Auth::factory()->create(['email' => 'test@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $get_user = $this->userService->getUserByAuthId($auth->id);
        $this->assertEquals($user->id, $get_user->id);

    }

    public function testCreateUser()
    {
        $this->userService = $this->app->make('App\Services\User\UserService');
        $name = 'kensuke_test';
        $birth_day =  new Carbon('1999-01-19');
        $auth = Auth::factory()->create(['email' => 'test@gmail.com', 'password' => 'pass']);

        $this->userService->createUser($name, $birth_day, $auth);

        $user = User::where(['auth_id' => $auth->id])->get()->first();

        $this->assertEquals($name, $user->name);
        $this->assertEquals($birth_day, $user->birth_day);

    }
}
