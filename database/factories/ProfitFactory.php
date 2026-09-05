<?php

namespace Database\Factories;

use App\Models\Profit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfitFactory extends Factory
{
    protected $model = Profit::class;

    public function definition(): array
    {
        return [
            'libelle' => strtoupper($this->faker->unique()->word()),
        ];
    }
}
