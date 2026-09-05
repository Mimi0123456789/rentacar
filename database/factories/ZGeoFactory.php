<?php

namespace Database\Factories;

use App\Models\ZGeo;
use App\Models\Ocean;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZGeoFactory extends Factory
{
    protected $model = ZGeo::class;

    public function definition(): array
    {
        return [
            'z_lib_court' => strtoupper($this->faker->unique()->lexify('Z??')),
            'z_lib_long'  => $this->faker->city(),
            'ocean_id'    => Ocean::factory(),
        ];
    }
}
