<?php

namespace Tests\Feature\Api;

use App\Models\Auth;
use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\ExerciseDetail;
use App\Models\ExerciseType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MeTest extends BaseTestCase
{
    public function testMeSuccess()
    {
        $email = 'testMeSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/me');

        $response->assertOk();
    }

    public function testGetMeAuth()
    {
        $email = 'testGetMeAuth@gmail.com';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/me/auth');

        $response->assertOk();
    }

    public function testExerciseType()
    {
        $email = 'testGetMeAuth@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        for ($i = 0; $i < 3; $i++) {
            $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => $i]);
            for ($j=0; $j<3; $j++) {
                ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => $i * $j]);
            }
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/me/exercise_types');

        $response->assertOk();
    }

    public function testPastExerciseSuccess()
    {
        $email = 'testPastExerciseSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/exercise_types/past_exercises/' . $exercise_type->id . '?' . 'page=' . 1);

        $response->assertOk();
    }

    public function testChangePasswordSuccess()
    {
        $email = 'testChangePasswordSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $password = 'chanegPassword10';
        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $new_password = 'newPassword10';
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/password', [
                'old_password' => $password,
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ]);

        $response->assertOk();
    }

    public function testChangePasswordFailWrongPassword()
    {
        $email = 'testChangePasswordFailWrongPassword@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $password = 'chanegPassword10';
        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $new_password = 'newPassword10';
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/password', [
                'old_password' => $password . 'wrong',
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ]);

        $response->assertStatus(500);
    }

    public function testChangePasswordFailWrongConfirmPassword()
    {
        $email = 'testChangePasswordFailWrongConfirmPassword@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $password = 'chanegPassword10';
        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $new_password = 'newPassword10';
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/password', [
                'old_password' => $password,
                'new_password' => $new_password,
                'confirm_password' => $new_password . 'worng'
            ]);

        $response->assertStatus(403);
    }

    public function testChangePasswordFailPasswordNull()
    {
        $email = 'testChangePasswordFailWrongConfirmPassword@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $password = 'chanegPassword10';
        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $new_password = 'newPassword10';
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/password', [
                'old_password' => '',
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ]);

        $response->assertStatus(422);
    }

    public function testChangePasswordFailNewPasswordNull()
    {
        $email = 'testChangePasswordFailWrongConfirmPassword@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $password = 'chanegPassword10';
        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $new_password = '';
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/password', [
                'old_password' => '',
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ]);

        $response->assertStatus(422);
    }

    public function testChangePasswordFailExceedMax()
    {
        $email = 'testChangePasswordFailWrongConfirmPassword@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $password = 'chanegPassword10';
        $auth = Auth::factory()->create(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $new_password = '1111111111wwwwwwwwww2222222222eeeeeeeeee3333333333rrrrrrrrrr4';
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/me/password', [
                'old_password' => $password,
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ]);

        $response->assertStatus(422);
    }
}