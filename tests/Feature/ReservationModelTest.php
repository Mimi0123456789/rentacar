<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\CreatesRentacarData;
use Tests\TestCase;

class ReservationModelTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRentacarData;

    public function test_une_reservation_est_associee_a_un_utilisateur_et_a_une_voiture(): void
    {
        $utilisateur = $this->createUser();
        $voiture = $this->createVoiture();

        $reservation = $this->createReservation(
            $utilisateur,
            $voiture
        );

        $this->assertTrue(
            $reservation->user->is($utilisateur)
        );

        $this->assertTrue(
            $reservation->voiture->is($voiture)
        );

        $this->assertTrue(
            $utilisateur->reservations->contains($reservation)
        );

        $this->assertTrue(
            $voiture->reservations->contains($reservation)
        );
    }

    public function test_une_reservation_peut_exister_sans_voiture(): void
    {
        $utilisateur = $this->createUser();

        $reservation = $this->createReservation(
            $utilisateur,
            null
        );

        $this->assertNull($reservation->voiture_id);
        $this->assertNull($reservation->voiture);
    }

    public function test_les_conversions_de_types_d_une_reservation_sont_appliquees(): void
    {
        $utilisateur = $this->createUser();

        $reservation = $this->createReservation(
            $utilisateur,
            null,
            [
                'nb_passagers' => 3,
                'bagages' => 1,
            ]
        );

        $this->assertInstanceOf(
            \Illuminate\Support\Carbon::class,
            $reservation->date_debut
        );

        $this->assertInstanceOf(
            \Illuminate\Support\Carbon::class,
            $reservation->date_fin
        );

        $this->assertIsInt(
            $reservation->nb_passagers
        );

        $this->assertIsBool(
            $reservation->bagages
        );

        $this->assertTrue(
            $reservation->bagages
        );
    }

    public function test_la_suppression_d_un_utilisateur_supprime_ses_reservations(): void
    {
        $utilisateur = $this->createUser();

        $reservation = $this->createReservation(
            $utilisateur
        );

        $utilisateur->delete();

        $this->assertDatabaseMissing(
            'reservations',
            [
                'id' => $reservation->id,
            ]
        );
    }

    public function test_la_suppression_d_une_voiture_conserve_la_reservation_sans_voiture(): void
    {
        $utilisateur = $this->createUser();
        $voiture = $this->createVoiture();

        $reservation = $this->createReservation(
            $utilisateur,
            $voiture
        );

        $voiture->delete();

        $this->assertDatabaseHas(
            'reservations',
            [
                'id' => $reservation->id,
                'voiture_id' => null,
            ]
        );
    }
}