<?php

namespace Tests\Feature\Api;

use App\Models\Auth;
use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\ExerciseDetail;
use App\Models\ExerciseType;
use App\Models\PlanExercise;
use App\Models\PlanExerciseDetail;
use App\Models\User;
use App\Models\WeekDay;

class PlanExerciseTest extends BaseTestCase
{
    public function testIndexSuccess()
    {
        $email = 'testIndexSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_days = WeekDay::factory()->count(7)->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 3;
        foreach ($week_days as $week_day) {
            $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
            for ($i = 0; $i < $set; $i++) {
                PlanExerciseDetail::factory()->create(
                    [
                        'plan_exercise_id' => $plan_exercise->id,
                        'rep' => 3,
                        'weight' => 10
                    ]
                );
            }
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/exercise_plans/');

        $response->assertOk();
    }

    public function testGetPlanExerciseSuccess()
    {
        $email = 'testGetPlanExerciseSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 3;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        for ($i = 0; $i < $set; $i++) {
            PlanExerciseDetail::factory()->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/exercise_plans/exercises?week_day_id=' . $week_day->id);

        $response->assertOk();
    }

    public function testStoreSuccess()
    {
        $email = 'testStoreSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $new_plan_exercises = [
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercise_plans/exercises', [
                'exercise_type_id' => $exercise_type->id,
                'exercise_details' => $new_plan_exercises,
                'week_day_id' => $week_day->id
            ]);

        $response->assertOk();

        $plan_exercise = PlanExercise::where(['user_id' => $user->id, 'week_day_id' => $week_day->id]);
        $this->assertNotEmpty($plan_exercise);
    }

    public function testStoreFailExerciseTypeIdNull()
    {
        $email = 'testStoreFailExerciseTypeIdNull@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $new_plan_exercises = [
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercise_plans/exercises', [
                'exercise_type_id' => null,
                'exercise_details' => $new_plan_exercises,
                'week_day_id' => $week_day->id
            ]);

        $response->assertStatus(422);
    }

    public function testStoreFailExerciseDetailNull()
    {
        $email = 'testStoreFailExerciseDetailNull@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $new_plan_exercises = [];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercise_plans/exercises', [
                'exercise_type_id' => $exercise_type->id,
                'exercise_details' => $new_plan_exercises,
                'week_day_id' => $week_day->id
            ]);

        $response->assertStatus(422);
    }

    public function testStoreFailExerciseWeekDayNull()
    {
        $email = 'testStoreFailExerciseWeekDayNull@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $key_token = $this->setAuthToken($auth);

        $new_plan_exercises = [
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercise_plans/exercises', [
                'exercise_type_id' => $exercise_type->id,
                'exercise_details' => $new_plan_exercises,
                'week_day_id' => null
            ]);

        $response->assertStatus(422);
    }

    public function testEditSuccess()
    {
        $email = 'testEditSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 3;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        for ($i = 0; $i < $set; $i++) {
            PlanExerciseDetail::factory()->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->getJson('/api/exercise_plans/exercises/' . $plan_exercise->id);

        $response->assertOk();
        $this->assertEquals($plan_exercise->id, $response->json(['exercise_id']));
    }

    public function testUpdateSuccess()
    {
        $email = 'testUpdateSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 2;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        for ($i = 0; $i < $set; $i++) {
            PlanExerciseDetail::factory()->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );
        }

        $key_token = $this->setAuthToken($auth);

        $new_plan_exercises = [
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
            [
                'rep' => '10',
                'weight' => '20.5'
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/exercise_plans/exercises/' . $plan_exercise->id, [
                'exercise_details' => $new_plan_exercises
            ]);

        $response->assertOk();
        $updated_plan_exercises = PlanExerciseDetail::where('plan_exercise_id',  $plan_exercise->id)->get();
        $this->assertEquals(3, $updated_plan_exercises->count());
    }

    public function testUpdateFailWrongRequest()
    {
        $email = 'testUpdateSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 2;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        for ($i = 0; $i < $set; $i++) {
            PlanExerciseDetail::factory()->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );
        }

        $key_token = $this->setAuthToken($auth);

        $new_plan_exercises = [];

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/exercise_plans/exercises/' . $plan_exercise->id, [
                'exercise_details' => $new_plan_exercises
            ]);

        $response->assertStatus(422);
    }

    public function testDeleteSuccess()
    {
        $email = 'testUpdateSuccess@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 2;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        for ($i = 0; $i < $set; $i++) {
            PlanExerciseDetail::factory()->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );
        }

        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->deleteJson('/api/exercise_plans/' . $plan_exercise->id);

        $response->assertOk();

        $deleted_plan_exercise = PlanExercise::where(['user_id' => $user->id, 'week_day_id' => $week_day->id])->get();
        $this->assertEmpty($deleted_plan_exercise);
    }

    public function testDeleteFailWrongOwner()
    {
        $email = 'testDeleteFailWrongOwner@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 2;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        for ($i = 0; $i < $set; $i++) {
            PlanExerciseDetail::factory()->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );
        }

        $request_auth = Auth::factory()->create(['email' => 'wrong' . $email, 'password' => 'pass']);
        $request_user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $request_auth->id]);

        $key_token = $this->setAuthToken($request_auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->deleteJson('/api/exercise_plans/' . $plan_exercise->id);

        $response->assertStatus(403);
    }

    public function testAddPlanExercise()
    {
        $email = 'testAddPlanExercise@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 2;
        $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set]);
        $plan_exercise_details = PlanExerciseDetail::factory()->count($set)->create(
            [
                'plan_exercise_id' => $plan_exercise->id,
                'rep' => 3,
                'weight' => 10
            ]
        );


        $key_token = $this->setAuthToken($auth);

        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->postJson('/api/exercises/add_plan_exercises', [
                'week_day_id' => $week_day->id
            ]);

        $response->assertOk();

        $exercise = Exercise::where(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id])->get()->first();
        $this->assertEquals($plan_exercise->set, $exercise->set);

        $exercise_details = ExerciseDetail::where(['exercise_id' => $exercise->id])->get();
        $this->assertEquals($plan_exercise_details->count(), $exercise_details->count());
    }

    public function testSortRequest()
    {
        $email = 'testSortRequest@gmail.com';
        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => $email, 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create();
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $set = 2;

        $target_exercises = [];
        for ($i = 1; $i <= 3; $i++){
            $plan_exercise = PlanExercise::factory()->create(['user_id' => $user->id, 'week_day_id' => $week_day->id, 'exercise_type_id' => $exercise_type->id, 'set' => $set, 'sort_index' => $i]);
            PlanExerciseDetail::factory()->count($set)->create(
                [
                    'plan_exercise_id' => $plan_exercise->id,
                    'rep' => 3,
                    'weight' => 10
                ]
            );

            $target_exercises[] = [
                'exercise_id' => $plan_exercise->id,
                'sort_index' => $plan_exercise->sort_index,
                'update_sort_id' => $i
            ];

        }

        $key_token = $this->setAuthToken($auth);
        $response = $this->withHeaders(['Authorization' => 'Bearer:' . $key_token])
            ->putJson('/api/exercise_plans/exercises/sort/' . $week_day->id, [
                'exercises' => $target_exercises
            ]);

        $response->assertOk();

    }
}