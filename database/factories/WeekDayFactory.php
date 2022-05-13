<?php

namespace Database\Factories;

use App\Models\WeekDay as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WeekDayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Model::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->title()
        ];
    }
}
