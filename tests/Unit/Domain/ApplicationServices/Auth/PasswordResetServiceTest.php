<?php

namespace Tests\Unit\Domain\ApplicationServices\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

use App\Models\Auth;
use App\Mail\PasswordReset;


class PasswordResetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $app;
    private $passwordResetService;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testSendMail()
    {
        Mail::fake();
        $this->passwordResetService = $this->app->make('Domain\ApplicationServices\Auth\PasswordResetService');

        $email = 'testSendMail@gmail.com';
        Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $this->passwordResetService->sendMail($email);

        Mail::assertSent(PasswordReset::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });

        Mail::assertSent(PasswordReset::class, 1);
    }

    public function testResetPassword()
    {
        Mail::fake();
        $this->passwordResetService = $this->app->make('Domain\ApplicationServices\Auth\PasswordResetService');

        $email = 'testResetPassword@gmail.com';
        $old_password = 'pass';
        $key = uniqid($email, true);
        Redis::set($key, $email);
        Auth::factory()->create(['email' => $email, 'password' => $old_password]);

        $auth_code = base64_encode($key);

        $new_password = 'test1234';
        $this->passwordResetService->resetPassword($auth_code, $new_password);

        /** メールが送信されるか */
        Mail::assertSent(PasswordReset::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
        Mail::assertSent(PasswordReset::class, 1);

        /** パスワードが更新されているか */
        $auth = Auth::where('email', $email)->first();
        $this->assertFalse(Hash::check($old_password, $auth->password));
        $this->assertTrue(Hash::check($new_password, $auth->password));

    }
}
