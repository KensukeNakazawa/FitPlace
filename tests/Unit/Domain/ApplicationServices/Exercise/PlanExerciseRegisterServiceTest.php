<?php

namespace Tests\Unit\Domain\ApplicationServices\Exercise;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Auth;
use App\Models\User;
use App\Models\Exercise;
use App\Models\ExerciseType;
use App\Models\BodyPart;
use App\Models\PlanExercise;
use App\Models\PlanExerciseDetail;
use App\Models\WeekDay;


class PlanExerciseRegisterServiceTest extends TestCase
{
    use RefreshDatabase;

    private $planExerciseRegisterService;

    public function testRegister()
    {
        $this->planExerciseRegisterService = $this->app->make('Domain\ApplicationServices\Exercise\PlanExerciseRegisterService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);
        $week_day = WeekDay::factory()->create(['name' => 'Monday']);
        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $plan_exercise = PlanExercise::factory()->create(
            [
                'user_id' => $user->id,
                'exercise_type_id' => $exercise_type->id,
                'week_day_id' => $week_day->id,
                'set' => 3
            ]
        );
        for ($i = 0; $i < 3; $i++) {
            PlanExerciseDetail::factory()->create(['plan_exercise_id' => $plan_exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $this->planExerciseRegisterService->register($user, $week_day->id);

        $exercise = Exercise::where(['user_id' => $user->id, 'exercise_type_id' => $exercise_type->id])->get()->first();

        $except_volume = 300;
        $except_max = 12.5;
        $except_set = 3;
        $this->assertEquals($except_volume, $exercise->volume);
        $this->assertEquals($except_max, $exercise->max);
        $this->assertEquals($except_set, $exercise->set);
    }
}
