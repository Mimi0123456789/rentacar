<?php

namespace Database\Factories;

use App\Models\DispoBat;
use Illuminate\Database\Eloquent\Factories\Factory;

class DispoBatFactory extends Factory
{
    protected $model = DispoBat::class;

    public function definition(): array
    {
        return [
            // DBAT + 3 lettres = 17 576 possibilités
            'dispo_lib_court' => strtoupper($this->faker->unique()->lexify('DBAT???')),
            'dispo_lib_long'  => $this->faker->sentence(4),
        ];
    }
}
