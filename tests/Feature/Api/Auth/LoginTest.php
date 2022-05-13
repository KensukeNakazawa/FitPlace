<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\Auth;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginSuccess()
    {
        $email = 'testLoginSuccess@gmail.com';
        $password = 'pass100';

        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => $password
        ]);

        $this->assertNotEmpty($response->json('access_token'));
        $response->assertStatus(200);
    }

    public function testLoginFail()
    {
        $email = 'test21LoginFail@gmail.com';
        $password = 'pass12100';

        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'passdasdsas'
        ]);

        $response->assertStatus(403);
    }

    public function testLoginメールアドレス空でリクエスト()
    {
        $email = 'testdas21EmailNull@gmail.com';
        $password = 'padsadss12100';
        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testLogin不正なメールアドレスでリクエスト()
    {
        $email = 'testdas21EmailNull@gmail.com';
        $password = 'padsadss12100';
        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'sdasda',
            'password' => $password
        ]);

        $response->assertStatus(422);
    }

    public function testLoginパスワード空でリクエスト()
    {
        $email = 'testdas21Password@gmail.com';
        $password = 'padsadss12100';
        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => ''
        ]);

        $response->assertStatus(422);
    }

    public function testLogin不正なパスワードでリクエスト()
    {
        $email = 'testdas21Password@gmail.com';
        $password = 'padsadss12100';
        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'sasa'
        ]);

        $response->assertStatus(422);
    }


    public function testLoginRateLimitFail()
    {
        $email = 'testratelimit21@gmail.com';
        $password = 'pass12100';

        Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => $email,
                'password' => 'passdasdsas'
            ]);
        }

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'passdasdsas'
        ]);

        $response->assertStatus(423);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNoLogin()
    {

        $response = $this->getJson('/api/auth/no_login');

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(
                [
                    'code' => '10',
                ]
            );
    }

    public function testNoLogin既にログイン済み()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer:a08407774fc171ddb245b40455698068375314dba057ccfb413c6e50c1391c26'])
            ->getJson('/api/auth/no_login');


        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(
                [
                    'code' => '20',
                ]
            );

    }
}
