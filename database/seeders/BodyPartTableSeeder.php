<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\BodyPart;

class BodyPartTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $body_parts = config('default.BODY_PARTS');

        $index = 1;
        foreach ($body_parts as $body_part) {
            BodyPart::create(
                [
                    'name' => $body_part,
                    'index' => $index
                ]
            );
            $index ++;
        }
    }
}
