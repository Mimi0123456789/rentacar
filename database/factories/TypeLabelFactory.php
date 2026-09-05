<?php

namespace Database\Factories;

use App\Models\TypeLabel;
use App\Models\Mention;
use App\Models\DispoBat;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeLabelFactory extends Factory
{
    protected $model = TypeLabel::class;

    public function definition(): array
    {
        return [
            'lab_lib' => strtoupper($this->faker->unique()->word()),
            'priorite' => $this->faker->numberBetween(1, 5),
            'mention_id' => Mention::factory(),
            'dispobat_id' => DispoBat::factory(),
            'position_id' => Position::factory(),
            'couleur' => $this->faker->hexColor(),
        ];
    }
}
