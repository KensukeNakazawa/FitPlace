<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\Exercise;
use App\Models\ExerciseDetail;

class ExerciseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') !== 'production') {
            $date = new Carbon();
            $date = $date->subDay(2);
            for ($index = 0; $index <= 20; $index++) {
                $set = 3;
                $sort_index = 1;
                $exercise = Exercise::factory()->create(
                    [
                        'user_id' => 1,
                        'exercise_type_id' => 1,
                        'set' => $set,
                        'sort_index' => $sort_index,
                        'volume' => 1800,
                        'max' => 80,
                        'exercise_at' => $date->addDays($index)
                    ]
                );
                for ($j = 0; $j < $set; $j++) {
                    ExerciseDetail::factory()->create(
                        [
                            'exercise_id' => $exercise->id,
                            'rep' => 10,
                            'weight' => 60
                        ]
                    );
                }

                $exercise = Exercise::factory()->create(
                    [
                        'user_id' => 1,
                        'exercise_type_id' => 2,
                        'set' => $set,
                        'sort_index' => $sort_index+1,
                        'volume' => 1800,
                        'max' => 80,
                        'exercise_at' => $date->addDays($index)
                    ]
                );
                for ($j = 0; $j < $set; $j++) {
                    ExerciseDetail::factory()->create(
                        [
                            'exercise_id' => $exercise->id,
                            'rep' => 10,
                            'weight' => 60
                        ]
                    );
                }

            }
        }
    }
}