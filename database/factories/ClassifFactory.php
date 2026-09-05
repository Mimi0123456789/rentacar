<?php

namespace Database\Factories;

use App\Models\Classif;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassifFactory extends Factory
{
    protected $model = Classif::class;

    public function definition(): array
    {
        return [
            'libelle_court' => strtoupper($this->faker->unique()->lexify('C??')),
            'libelle_long'  => $this->faker->sentence(4),
        ];
    }
}
