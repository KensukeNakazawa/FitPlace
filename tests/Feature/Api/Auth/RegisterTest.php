<?php

namespace Tests\Feature\Api\Auth;

use App\Models\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Tests\Feature\Api\BaseTestCase;

use App\Mail\SampleNotification;

class RegisterTest extends BaseTestCase
{

    public function testRegisterSuccess()
    {
        Mail::fake();

        $email = 'testRegisterSuccess@gmail.com';
        $password = 'password10';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        Mail::assertSent(SampleNotification::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });

        $response->assertOk();
        $auth = Auth::where('email', $email)->get();
        $this->assertNotEmpty($auth);
    }

    public function testRegisterFailEmailNull()
    {
        $email = '';
        $password = 'password10';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testRegisterFailEmailWrongType()
    {
        $email = 'testRegisterFailEmailWrongType';
        $password = 'password10';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testRegisterFailPasswordNull()
    {
        $email = 'testRegisterFailPasswordNull@gmail.com';
        $password = '';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testRegisterFailPasswordUnderMin()
    {
        $email = 'testRegisterFailPasswordNull@gmail.com';
        $password = 'test1';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testRegisterSuccessPasswordUnderMax()
    {
        Mail::fake();
        $email = 'testRegisterFailPasswordNull@gmail.com';
        $password = 'test1test1test1test1test1test1test1test1test1test1';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        Mail::assertSent(SampleNotification::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
        $response->assertStatus(200);
    }

    public function testRegisterFailExceedMax()
    {
        $email = 'testRegisterFailPasswordNull@gmail.com';
        $password = 'test1test1test1test1test1test1test1test1test1test1t';

        $response = $this->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testAuthorizeCodeSuccess()
    {
        $email = 'testAuthorizeCodeSuccess@gmail.com';

        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $auth_code = rand(10000, 99999);
        $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $email;
        Redis::set($key, $auth_code);

        $response = $this->postJson('/api/auth/authorize_code', [
                'auth_id' => $email,
                'auth_code' => '' . $auth_code
            ]);

        $response->assertOk();
    }

    public function testAuthorizeCodeFailEmailNull()
    {
        $email = 'testAuthorizeCodeFailEmailNull@gmail.com';

        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $auth_code = rand(10000, 99999);
        $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $email;
        Redis::set($key, $auth_code);

        $response = $this->postJson('/api/auth/authorize_code', [
            'auth_id' => '',
            'auth_code' => '' . $auth_code
        ]);

        $response->assertStatus(422);
    }

    public function testAuthorizeCodeFailAuthCodeUnderNum()
    {
        $email = 'testAuthorizeCodeFailAuthCodeUnderNum@gmail.com';

        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $auth_code = rand(10000, 99999);
        $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $email;
        Redis::set($key, $auth_code);

        $response = $this->postJson('/api/auth/authorize_code', [
            'auth_id' => $email,
            'auth_code' => '1234'
        ]);

        $response->assertStatus(422);
    }

    public function testAuthorizeCodeFailAuthCodeExceedNum()
    {
        $email = 'testAuthorizeCodeFailAuthCodeExceedNum@gmail.com';

        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $auth_code = rand(10000, 99999);
        $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $email;
        Redis::set($key, $auth_code);

        $response = $this->postJson('/api/auth/authorize_code', [
            'auth_id' => $email,
            'auth_code' => '123456'
        ]);

        $response->assertStatus(422);
    }

    public function testAuthorizeCodeFailWrongAuthCode()
    {
        $email = 'testAuthorizeCodeFailAuthCodeExceedNum@gmail.com';

        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $auth_code = 12345;
        $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $email;
        Redis::set($key, $auth_code);

        $response = $this->postJson('/api/auth/authorize_code', [
            'auth_id' => $email,
            'auth_code' => '12343'
        ]);

        $response->assertStatus(403);

        $this->assertEquals(__('messages.auth_code_failed'), $response->json(['message']));
    }
}