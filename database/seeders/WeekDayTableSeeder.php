<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\WeekDay;

class WeekDayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $week_day_data = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        $week_days = [];
        foreach ($week_day_data as $week_day) {
            $week_days[] = WeekDay::create(
                [
                    'name' => $week_day
                ]
            );
        }

    }
}
