<?php

namespace Database\Factories;

use App\Models\Opcon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpconFactory extends Factory
{
    protected $model = Opcon::class;

    public function definition(): array
    {
        return [
            // OP + 3 lettres = 17 576 possibilités
            'opcon_lib_court' => strtoupper($this->faker->unique()->lexify('OP???')),
            'opcon_lib_long'  => $this->faker->sentence(3),
        ];
    }
}
