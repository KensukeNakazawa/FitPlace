<?php

namespace Tests\Unit\Domain\DomainServices;

use App\Models\Auth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $app;
    private $userService;

    public function testIsExistTrue()
    {
        $this->userService = $this->app->make('Domain\DomainServices\UserDomainService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => 'testIsExistTrue@gmail.com', 'password' => 'pass']);
        User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $is_exist = $this->userService->isExist($auth);
        $this->assertTrue($is_exist);
    }

    public function testIsExistFalse()
    {
        $this->userService = $this->app->make('Domain\DomainServices\UserDomainService');

        $auth = Auth::factory()->create(['email' => 'testIsExistTrue@gmail.com', 'password' => 'pass']);

        $is_exist = $this->userService->isExist($auth);
        $this->assertFalse($is_exist);
    }
}