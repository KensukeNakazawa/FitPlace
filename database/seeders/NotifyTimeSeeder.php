<?php

namespace Database\Seeders;

use App\Models\NotifyTime;
use Illuminate\Database\Seeder;


class NotifyTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NotifyTime::create(
            [
                'time' => 6,
            ],
        );

        NotifyTime::create(
            [
                'time' => 12,
            ],
        );

        NotifyTime::create(
            [
                'time' => 18,
            ]
        );
    }
}
