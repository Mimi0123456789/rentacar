<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'lib_position_court' => strtoupper($this->faker->unique()->word()),
            'lib_position_long'  => $this->faker->sentence(3),
        ];
    }
}
