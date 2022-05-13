<?php

namespace Tests\Feature\Api;

use App\Models\Auth;
use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\ExerciseDetail;
use App\Models\ExerciseType;
use App\Models\User;

class ExerciseTest extends BaseTestCase
{
    public function testGetExerciseSuccess()
    {
        $email = 'testGetExerciseSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/exercises');
        $response->assertOk();
    }

    public function testEditSuccess()
    {
        $email = 'testEditSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/exercises/' . $exercise->id);

        $response->assertStatus(200);
    }

    public function testUpdateSuccess()
    {
        $email = 'testUpdateSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $new_exercise_details = [
            [
                'rep' => 10,
                'weight' => 60
            ],
            [
                'rep' => 10,
                'weight' => 60
            ],
            [
                'rep' => 10,
                'weight' => 60
            ],
            [
                'rep' => 10,
                'weight' => 60
            ]
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/exercises/' . $exercise->id, [
                'exercise_details' => $new_exercise_details
            ]);

        $response->assertStatus(200);
        $updated_exercise_details = ExerciseDetail::where('exercise_id', $exercise->id)->get();
        $this->assertEquals(4, $updated_exercise_details->count());
    }

    public function testDeleteSuccess()
    {
        $email = 'testUpdateSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->deleteJson('/api/exercises/' . $exercise->id);

        $response->assertStatus(200);

        $updated_exercise_details = ExerciseDetail::where('exercise_id', $exercise->id)->get();
        $this->assertEmpty($updated_exercise_details);
    }

    public function testUpdateExerciseDetailsNull()
    {
        $email = 'testUpdateSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $new_exercise_details = [];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/exercises/' . $exercise->id, [
                'exercise_details' => $new_exercise_details
            ]);

        $response->assertStatus(422);
    }

    public function testStoreExerciseSuccess()
    {
        $email = 'testStoreExerciseSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercises', [
                'exercise_type_id' => $exercise_type->id,
                'exercise_details' => [['id' => 0, 'rep' => "10", 'weight' => "60"]],
                'date' => '2021-09-03'
            ]);

        $response->assertOk();
    }

    public function testStoreExerciseExerciseTypeNull()
    {
        $email = 'testStoreExerciseExerciseTypeNull@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercises', [
                'exercise_type_id' => null,
                'exercise_details' => [['id' => 0, 'rep' => "10", 'weight' => "60"]],
                'date' => '2021-09-03'
            ]);

        $response->assertStatus(422);
    }

    public function testStoreExerciseExerciseDetailNull()
    {
        $email = 'testStoreExerciseExerciseTypeNull@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercises', [
                'exercise_type_id' => $exercise_type->id,
                'exercise_details' => [],
                'date' => '2021-09-03'
            ]);

        $response->assertStatus(422);
    }

    public function testStoreExerciseDateNull()
    {
        $email = 'testStoreExerciseExerciseTypeNull@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';
        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $key_token = $this->setAuthToken($auth);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercises', [
                'exercise_type_id' => $exercise_type->id,
                'exercise_details' => [['id' => 0, 'rep' => "10", 'weight' => "60"]],
                'date' => ''
            ]);

        $response->assertStatus(422);
    }

    public function testSortRequest()
    {
        $email = 'testSortRequest@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $target_exercises = [];
        for ($index=1; $index <= 3; $index++) {
            $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3, 'sort_index' => $index]);
            $exercise_details = [];
            for ($j_index=0; $j_index < 3; $j_index++){
                $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
            }

            $target_exercises[] = [
                'exercise_id' => $exercise->id,
                'sort_index' => $exercise->sort_index,
                'update_sort_index' => $index
            ];
        }

        $key_token = $this->setAuthToken($auth);
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/exercises/sort/', [
                'exercises' => $target_exercises
            ]);

        $response->assertOk();
    }
}