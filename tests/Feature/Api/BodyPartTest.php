<?php

namespace Tests\Feature\Api;

use App\Models\Auth;
use App\Models\BodyPart;
use App\Models\User;

class BodyPartTest extends BaseTestCase
{
    public function testIndexSuccess()
    {
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testIndexSuccess@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/body_parts');

        $response->assertOk();
    }

    public function testIndexBadRequest()
    {
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testIndexBadRequest@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . 'testesets'])
            ->getJson('/api/body_parts');

        $response->assertStatus(403);
    }

    public function testStoreExerciseTypeSuccess()
    {
        $name = 'kensuke_tesst';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testStoreExerciseTypeSuccess@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/body_parts', [
                'body_part_id' => $body_part->id,
                'exercise_type_name' => 'テストトレーニング種目が20文字で入力2'
            ]);

        $response->assertOk();
    }

    public function testStoreExerciseTypeBodyPartIdNull()
    {
        $name = 'kensuke_tesst';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testStoreExerciseTypeBodyPartIdNull@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/body_parts', [
                'body_part_id' => null,
                'exercise_type_name' => '腹筋'
            ]);

        $response->assertStatus(422);
    }

    public function testStoreExerciseTypeExerciseTypeNameNull()
    {
        $name = 'kensuke_tesst';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testStoreExerciseTypeExerciseTypeNameNull@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/body_parts', [
                'body_part_id' => $body_part->id,
                'exercise_type_name' => ''
            ]);

        $response->assertStatus(422);
    }

    public function testStoreExerciseTypeExerciseTypeNameExceedMax()
    {
        $name = 'kensuke_tesst';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testStoreExerciseTypeSuccess@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/body_parts', [
                'body_part_id' => $body_part->id,
                'exercise_type_name' => 'テストトレーニング種目が21文字以上で入力'
            ]);

        $response->assertStatus(422);
    }
}