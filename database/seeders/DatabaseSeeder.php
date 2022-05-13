<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                BodyPartTableSeeder::class,
                AuthTableSeeder::class,
                WeekDayTableSeeder::class,
                UserTableSeeder::class,
                ExerciseTableSeeder::class,
                AdminTableSeeder::class,
                NotifyTimeSeeder::class
            ]
        );
    }
}
