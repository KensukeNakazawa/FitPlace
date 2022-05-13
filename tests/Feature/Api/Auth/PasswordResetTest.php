<?php

namespace Tests\Feature\Api\Auth;

use App\Mail\PasswordReset;
use App\Models\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Tests\Feature\Api\BaseTestCase;


class PasswordResetTest extends BaseTestCase
{
    public function testSendMailForPasswordResetSuccess()
    {
        Mail::fake();

        $email = 'testSendMailForPasswordResetSuccess@gmail.com';
        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $response = $this->postJson('/api/auth/password_reset/send_mail', [
            'email' => $email
        ]);

        Mail::assertSent(PasswordReset::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });

        $response->assertOk();
    }

    public function testSendMailForPasswordResetFailEmailNull()
    {
        $email = 'testSendMailForPasswordReset@gmail.com';
        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $response = $this->postJson('/api/auth/password_reset/send_mail', [
            'email' => ''
        ]);

        $response->assertStatus(422);

    }

    public function testPasswordResetSuccess()
    {
        Mail::fake();
        $email = 'testPasswordResetSuccess@gmail.com';
        $password = 'password10';

        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $key = uniqid($email, true);
        Redis::set($key, $email);

        $new_password = 'new' . $password;
        $response = $this->putJson('/api/auth/password_reset/reset', [
            'auth_code' => base64_encode($key),
            'new_password' => $new_password
        ]);

        Mail::assertSent(PasswordReset::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });

        $response->assertOk();
        $updated_auth = Auth::find($auth->id);
        $this->assertTrue(Hash::check($new_password, $updated_auth->password));
    }

    public function testPasswordResetFailPasswordNull()
    {
        $email = 'testPasswordResetFailPasswordNull@gmail.com';
        $password = 'password10';

        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $key = uniqid($email, true);
        Redis::set($key, $email);

        $response = $this->putJson('/api/auth/password_reset/reset', [
            'auth_code' => base64_encode($key),
            'new_password' => ''
        ]);

        $response->assertStatus(422);
    }

    public function testPasswordResetFailPasswordUnderMin()
    {
        $email = 'testPasswordResetFailPasswordUnderMin@gmail.com';
        $password = 'password10';

        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $key = uniqid($email, true);
        Redis::set($key, $email);

        $response = $this->putJson('/api/auth/password_reset/reset', [
            'auth_code' => base64_encode($key),
            'new_password' => 'test1'
        ]);

        $response->assertStatus(422);
    }

    public function testPasswordResetFailPasswordExceedMax()
    {
        $email = 'testPasswordResetFailPasswordExceedMax@gmail.com';
        $password = 'password10';

        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $key = uniqid($email, true);
        Redis::set($key, $email);

        $response = $this->putJson('/api/auth/password_reset/reset', [
            'auth_code' => base64_encode($key),
            'new_password' => 'test1test1test1test1test1test1test1test1test1test11'
        ]);

        $response->assertStatus(422);
    }

    public function testPasswordResetFailCodeWrong()
    {
        Mail::fake();
        $email = 'testPasswordResetFailCodeWrong@gmail.com';
        $password = 'password10';

        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $key = uniqid($email, true);
        Redis::set($key, $email);

        $new_password = 'new' . $password;
        $response = $this->putJson('/api/auth/password_reset/reset', [
            'auth_code' => $key,
            'new_password' => $new_password
        ]);

        $response->assertStatus(404);
        $this->assertEquals('コードがありません', $response->json(['message']));
    }
}