<?php

namespace Tests\Unit\Services\Exercise;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\ExerciseDetail;
use App\Models\DeletedExerciseDetail;
use App\Models\ExerciseType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Auth;
use App\Models\User;

class ExerciseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $app;
    private $exerciseService;
    private $userService;

    public function testAddExercise()
    {
        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise_details = [
            [
                'weight' => 10,
                'rep' => 10
            ],
            [
                'weight' => 10,
                'rep' => 10
            ]
        ];

        $expect_volume = (10 * 10) + (10*10);
        $expect_max = (10 * 10) / 40 + 10;
        $expect_set = 2;

        $this->exerciseService->addExercise($user, $exercise_type->id, $exercise_details);

        $exercise_detail = ExerciseDetail::get()->first();
        $this->assertEquals($exercise_details[0]['weight'], $exercise_detail->weight);

        $exercise = Exercise::where(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id])->get()->first();
        $this->assertEquals($expect_volume, $exercise->volume);
        $this->assertEquals($expect_max, $exercise->max);
        $this->assertEquals($expect_set, $exercise->set);
    }

    public function testGetExerciseDateNull()
    {
        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseService');

        $name = 'testGetExerciseDateNull';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $exercise = Exercise::factory()->create(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id, 'set' => 3]);
        $exercise_details = ExerciseDetail::factory()->count(3)->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 30]);

        $exercises = $this->exerciseService->getExerciseForIndex($user);

        foreach ($exercises as $exercise) {
            $this->assertEquals($exercise['set'], 3);
            foreach ($exercise['exercise_details'] as $exercise_detail) {
                $this->assertEquals($exercise_detail['rep'], 10);
                $this->assertEquals($exercise_detail['weight'], 30);
            }
        }
    }

    /**
     * トレーニングの更新テスト(増やすパターン)
     */
    public function testUpdateExercise()
    {
        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseService');

        $name = 'testUpdateExercise';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testUpdateExercise@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id, 'set' => 3, 'volume' => 300, 'max' => 12.5]);
        for ($i = 0; $i < 3; $i++) {
            ExerciseDetail::factory()->create(
                [
                    'exercise_id' => $exercise->id,
                    'rep' => 10,
                    'weight' => 10
                ]
            );
        }

        /** 更新するデータの生成　*/
        $exercise_details = [
            [
                'rep' => 10,
                'weight' => 10
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
            [
                'rep' => 10,
                'weight' => 12.5
            ],
        ];

        $this->exerciseService->updateExercise($user, $exercise->id, $exercise_details);

        $updated_exercise = Exercise::find($exercise->id);
        $except_volume = 425;
        $except_max = 15.63;
        $except_set = 4;
        $this->assertEquals($except_volume, $updated_exercise->volume);
        $this->assertEquals($except_max, $updated_exercise->max);
        $this->assertEquals($except_set, $updated_exercise->set);
    }

    /**
     * トレーニング数は同じだけど、ボリュームが下がるパターン
     */
    public function testUpdateExerciseEqualNumMinus()
    {
        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseService');

        $name = 'testUpdateExercise';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testUpdateExercise@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id, 'set' => 3, 'volume' => 300, 'max' => 12.5]);
        for ($i = 0; $i < 3; $i++) {
            ExerciseDetail::factory()->create(
                [
                    'exercise_id' => $exercise->id,
                    'rep' => 10,
                    'weight' => 10
                ]
            );
        }

        /** 更新するデータの生成　*/
        $exercise_details = [
            [
                'rep' => 10,
                'weight' => 8
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
        ];

        $this->exerciseService->updateExercise($user, $exercise->id, $exercise_details);

        $updated_exercise = Exercise::find($exercise->id);
        $except_volume = 280;
        $except_max = 12.5;
        $except_set = 3;
        $this->assertEquals($except_volume, $updated_exercise->volume);
        $this->assertEquals($except_max, $updated_exercise->max);
        $this->assertEquals($except_set, $updated_exercise->set);
    }


    /**
     * トレーニング数は同じだけど、ボリュームが上がるパターン
     */
    public function testUpdateExerciseEqualNumPlus()
    {
        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseService');

        $name = 'testUpdateExercise';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testUpdateExercise@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id, 'set' => 3, 'volume' => 300, 'max' => 12.5]);
        for ($i = 0; $i < 3; $i++) {
            ExerciseDetail::factory()->create(
                [
                    'exercise_id' => $exercise->id,
                    'rep' => 10,
                    'weight' => 10
                ]
            );
        }

        /** 更新するデータの生成　*/
        $exercise_details = [
            [
                'rep' => 10,
                'weight' => 12.5
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
        ];

        $this->exerciseService->updateExercise($user, $exercise->id, $exercise_details);

        $updated_exercise = Exercise::find($exercise->id);
        $except_volume = 325;
        $except_max = 15.63;
        $except_set = 3;
        $this->assertEquals($except_volume, $updated_exercise->volume);
        $this->assertEquals($except_max, $updated_exercise->max);
        $this->assertEquals($except_set, $updated_exercise->set);
    }


    /**
     * トレーニング数が減るけど、1RMは上がる
     */
    public function testUpdateExerciseMinus()
    {
        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseService');

        $name = 'testUpdateExerciseMinus';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testUpdateExerciseMinus@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id, 'set' => 3, 'volume' => 300, 'max' => 12.5]);
        $original_exercise_details = [];
        for ($i = 0; $i < 3; $i++) {
            $original_exercise_details[] = ExerciseDetail::factory()->create(
                [
                    'exercise_id' => $exercise->id,
                    'rep' => 10,
                    'weight' => 10
                ]
            );
        }

        /** 更新するデータの生成　*/
        $exercise_details = [
            [
                'rep' => 10,
                'weight' => 12.5
            ],
            [
                'rep' => 10,
                'weight' => 10
            ],
        ];

        $this->exerciseService->updateExercise($user, $exercise->id, $exercise_details);

        $updated_exercise = Exercise::find($exercise->id);
        $except_volume = 225;
        $except_max = 15.63;
        $except_set = 2;
        $this->assertEquals($except_volume, $updated_exercise->volume);
        $this->assertEquals($except_max, $updated_exercise->max);
        $this->assertEquals($except_set, $updated_exercise->set);

        $deleted_exercise_detail = DeletedExerciseDetail::where(['exercise_detail_id' => $original_exercise_details[2]->id])->get()->first();
        $this->assertEquals($original_exercise_details[2]->rep, $deleted_exercise_detail->rep);
    }
}