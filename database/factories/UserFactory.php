<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'grd_id' => null,
            'prenom' => $this->faker->firstName(),
            'nom' => $this->faker->lastName(),

            // ⚠️ unique obligatoire
            'mail' => $this->faker->unique()->safeEmail(),

            'login' => $this->faker->unique()->userName(),

            // 🔐 mot de passe valide Laravel
            'password_hash' => Hash::make('password'),

            'droit' => 'N5',
        ];
    }

    /**
     * États pratiques
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'droit' => 'ADMIN',
        ]);
    }

    public function n3(): static
    {
        return $this->state(fn () => [
            'droit' => 'N3',
        ]);
    }
}
