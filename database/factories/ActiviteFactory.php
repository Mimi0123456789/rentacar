<?php

namespace Database\Factories;

use App\Models\Activite;
use App\Models\Bateau;
use App\Models\TypeLabel;
use App\Models\Profit;
use App\Models\Opcon;
use App\Models\ZGeo;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActiviteFactory extends Factory
{
    protected $model = Activite::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $end = (clone $start)->modify('+' . rand(1, 5) . ' days');

        return [
            'titre' => $this->faker->sentence(3),
            'bateau_id' => Bateau::factory(),
            'start' => $start,
            'end' => $end,
            'dispo_id' => 1,
            'class_id' => 1,
            'priorite' => rand(1, 5),
            'position_id' => Position::factory(),
            'label_id' => TypeLabel::factory(),
            'profit_id' => Profit::factory(),
            'opcon_id' => Opcon::factory(),
            'couleur' => $this->faker->hexColor(),
            'z_geo_id' => ZGeo::factory(),
            'commentaire' => null,
            'version' => 1,
        ];
    }
}
