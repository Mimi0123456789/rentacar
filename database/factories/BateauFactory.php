<?php

namespace Database\Factories;

use App\Models\Bateau;
use Illuminate\Database\Eloquent\Factories\Factory;

class BateauFactory extends Factory
{
    protected $model = Bateau::class;

    public function definition(): array
    {
        return [
            'immat' => strtoupper($this->faker->bothify('FR-####')),
            'type' => $this->faker->randomElement(['RUB', 'SUF']),
            'nom' => $this->faker->word(),
            'qualif_lan' => $this->faker->boolean(),
            'qualif_lsm' => $this->faker->boolean(),
            'qualif_os' => $this->faker->boolean(),
            'qualif_ele' => $this->faker->boolean(),
            'armes' => $this->faker->boolean(),
            'tube_1' => 0,
            'tube_2' => 0,
            'tube_3' => 0,
            'tube_4' => 0,
            'trans_ime' => 1,
            'trans_filaire' => false,
            'trans_syr' => false,
            'ops' => true,
            'commentaire' => null,
            'actif' => true,
        ];
    }
}
