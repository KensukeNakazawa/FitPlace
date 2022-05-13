<?php

namespace Tests\Feature\Api;

use App\Models\Auth;
use App\Models\User;

class UserTest extends BaseTestCase
{
    public function testCreateUser()
    {
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testCreateUser@gmail.com', 'password' => 'pass']);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/users/store', [
                'name' => $name,
                'birth_day' => $birth_day
            ]);

        $response->assertOk();
        $user = User::where('auth_id', $auth->id)->get()->first();
        $this->assertEquals($name, $user->name);
    }

    public function testCreateUserFailUserExist()
    {
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testCreateUser@gmail.com', 'password' => 'pass']);
        User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/users/store', [
                'name' => $name,
                'birth_day' => $birth_day
            ]);

        $response->assertStatus(409);
        $this->assertEquals(__('messages.user.already_exist'), $response->json(['message']));
    }

    public function testCreateUserFailNameNull()
    {
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testCreateUserFailNameNull@gmail.com', 'password' => 'pass']);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/users/store', [
                'name' => '',
                'birth_day' => $birth_day
            ]);

        $response->assertStatus(422);
    }

    public function testCreateUserFailNameExceedMax()
    {
        $name = 'kensuke_testasdewq232';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testCreateUserFailNameExceedMax@gmail.com', 'password' => 'pass']);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/users/store', [
                'name' => $name,
                'birth_day' => $birth_day
            ]);

        $response->assertStatus(422);
    }

    public function testCreateUserFailBirthDayNull()
    {
        $name = 'kensuke_test';
        $birth_day = '';

        $auth = Auth::factory()->create(['email' => 'testCreateUserFailBirthDayNull@gmail.com', 'password' => 'pass']);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/users/store', [
                'name' => $name,
                'birth_day' => $birth_day
            ]);

        $response->assertStatus(422);
    }

    public function testCreateUserFailBirthDayWrongType()
    {
        $name = 'kensuke_test';
        $birth_day = 'sdasdas';

        $auth = Auth::factory()->create(['email' => 'testCreateUserFailBirthDayNull@gmail.com', 'password' => 'pass']);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/users/store', [
                'name' => $name,
                'birth_day' => $birth_day
            ]);

        $response->assertStatus(422);
    }
}