<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;

class BaseTestCase extends TestCase
{
    use RefreshDatabase;

    public function setAuthToken($auth) {
        $original_string = env('JWT_SECRET') . $auth->email . uniqid();
        $key_token = hash('sha256', $original_string);
        $key_value = Crypt::encrypt(config('enum.AUTH_TYPE.EMAIL') . ':' .  $auth->email);

        Redis::set($key_token, $key_value);
        return $key_token;
    }
}