<?php

namespace Tests\Support;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Voiture;
use Illuminate\Support\Facades\Hash;

trait CreatesRentacarData
{
    protected function createUser(array $overrides = []): User
    {
        $donnees = array_merge([
            'name' => 'Utilisateur Test',
            'email' => 'test_' . uniqid() . '@example.com',
            'login' => 'test_' . uniqid(),
            'droit' => 'ADMINISTRATEUR',
            'password' => 'password123',
        ], $overrides);

        if (!Hash::needsRehash($donnees['password'])) {
            //
        } else {
            $donnees['password'] = Hash::make($donnees['password']);
        }

        return User::create($donnees);
    }

    protected function createVoiture(array $overrides = []): Voiture
    {
        return Voiture::create(array_merge([
            'immatriculation' => 'AA-' . random_int(100, 999) . '-AA',
            'marque' => 'Renault',
            'modele' => 'Clio',
            'kilometrage' => 10000,
            'statut' => 'disponible',
        ], $overrides));
    }

    protected function createReservation(User $user, ?Voiture $voiture = null, array $overrides = []): Reservation 
    {
        return Reservation::create(array_merge([
            'user_id' => $user->id,
            'voiture_id' => $voiture?->id,
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDays(2),
            'motif' => 'Déplacement professionnel',
            'nb_passagers' => 1,
            'bagages' => false,
            'statut' => 'en attente',
        ], $overrides));
    }
}