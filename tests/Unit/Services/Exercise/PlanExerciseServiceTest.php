<?php

namespace Tests\Unit\Domain\ApplicationServices\Exercise;

use App\Models\BodyPart;
use App\Models\ExerciseType;
use App\Models\WeekDay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Auth;
use App\Models\User;
use App\Models\PlanExercise;
use App\Models\PlanExerciseDetail;

class PlanExerciseServiceTest extends TestCase
{

    use RefreshDatabase;

    protected $app;
    private $planExerciseService;
    private $userService;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetPlanExerciseWithWeekDay()
    {
        $this->planExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\PlanExerciseService');

        $name = 'testGetPlanExerciseWithWeekDay_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetPlanExerciseWithWeekDay@gmail.com', 'password' => 'pass']);
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

        $result = $this->planExerciseService->getPlanExerciseWithWeekDay($user);
        $result_one = $result[0];
        $this->assertEquals($week_days->first()->id, $result_one['week_day_id']);
        $this->assertEquals(3, count($result_one['exercises'][0]["exercise_details"]));
    }

    /**
     * セットふやすパターン
     */
    public function testAddPlanExercise()
    {
        $this->planExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\PlanExerciseService');

        $name = 'testAddPlanExercise_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testAddPlanExercise@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);


        $week_day = WeekDay::factory()->create();
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

        $this->planExerciseService->addPlanExercise($user, $week_day->id, $exercise_type->id, $exercise_details);

        $exercise_detail = PlanExerciseDetail::get()->first();
        $exercise = PlanExercise::where(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id])->get()->first();
        $this->assertEquals($exercise_details[0]['weight'], $exercise_detail->weight);
        $this->assertEquals(2, $exercise->set);
    }

    public function testUpdatePlanExercise()
    {
        $this->planExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\PlanExerciseService');

        $name = 'testAddPlanExercise_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testAddPlanExercise@gmail.com', 'password' => 'pass']);
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

        $this->planExerciseService->updatePlanExercise($user, $plan_exercise->id, $exercise_details);
        $updated_plan_exercise = PlanExercise::find($plan_exercise->id);
        $except_set = 4;
        $this->assertEquals($except_set, $updated_plan_exercise->set);
    }


    /**
     * セット数減るパターン
     */
    public function testUpdatePlanExerciseNumDown()
    {
        $this->planExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\PlanExerciseService');

        $name = 'testUpdatePlanExerciseNumDown_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testUpdatePlanExerciseNumDown@gmail.com', 'password' => 'pass']);
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

        /** 更新するデータの生成　*/
        $exercise_details = [
            [
                'rep' => 10,
                'weight' => 10
            ],
            [
                'rep' => 10,
                'weight' => 10
            ]
        ];

        $this->planExerciseService->updatePlanExercise($user, $plan_exercise->id, $exercise_details);
        $updated_plan_exercise = PlanExercise::find($plan_exercise->id);
        $except_set = 2;
        $this->assertEquals($except_set, $updated_plan_exercise->set);
    }
}
