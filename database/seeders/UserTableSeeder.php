<?php

namespace Database\Seeders;

use App\Models\WeekDay;
use Illuminate\Database\Seeder;

use App\Models\Auth;
use App\Models\BodyPart;
use App\Models\User;
use App\Models\ExerciseType;
use App\Models\PlanExercise;
use App\Models\PlanExerciseDetail;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $auth = Auth::get()->first();
        $user = User::create(
            [
                'auth_id' => $auth->id,
                'name' => 'kensuke',
                'birth_day' => '1999-01-19'
            ]
        );

        $body_parts = BodyPart::get();
        $exercise_types = config('default.EXERCISE_TYPES');
        foreach ($body_parts as $index => $body_part) {
            foreach ($exercise_types[$index] as $j_index => $exercise_type) {
                ExerciseType::create(
                    [
                        'body_part_id' => $body_part->id,
                        'user_id' => $user->id,
                        'name' => $exercise_type,
                        'max_weight' => 0,
                        'sort_index' => $j_index + 1
                    ]
                );
            }
        }

        if (env('APP_ENV') !== 'production') {

            $week_days = WeekDay::get();
            foreach ($week_days as $week_day) {
                $plan_exercise = PlanExercise::factory()->create(
                    [
                        'user_id' => $user->id,
                        'exercise_type_id' => 1,
                        'week_day_id' => $week_day->id,
                        'set' => 3,
                        'sort_index' => 1
                    ]
                );

                for ($i = 0; $i < 3; $i++) {
                    PlanExerciseDetail::factory()->create(
                        [
                            'plan_exercise_id' => $plan_exercise->id,
                            'rep' => 10,
                            'weight' => 60
                        ]
                    );
                }
            }
        }

    }
}
