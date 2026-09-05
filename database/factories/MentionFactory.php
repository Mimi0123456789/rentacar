<?php

namespace Database\Factories;

use App\Models\Mention;
use Illuminate\Database\Eloquent\Factories\Factory;

class MentionFactory extends Factory
{
    protected $model = Mention::class;

    public function definition(): array
    {
        return [
            'ment_lib_court' => strtoupper($this->faker->unique()->lexify('M??')),
            'ment_lib_long'  => $this->faker->sentence(3),
        ];
    }
}
