<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesRentacarData;
use Tests\TestCase;

class VoitureControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRentacarData;

    public function test_un_utilisateur_authentifie_peut_consulter_la_page_des_voitures(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->get('/voitures')
            ->assertOk()
            ->assertViewIs('voitures.index')
            ->assertViewHas('voitures');
    }

    public function test_une_voiture_peut_etre_creee(): void
    {
        $acteur = $this->createUser();

        $reponse = $this->actingAs($acteur)->postJson('/voitures', [
            'immatriculation' => 'AB-123-CD',
            'marque' => 'Peugeot',
            'modele' => '308',
            'kilometrage' => 30000,
            'statut' => 'disponible',
        ]);

        $reponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('voiture.immatriculation', 'AB-123-CD')
            ->assertJsonPath('voiture.statut', 'disponible');

        $this->assertDatabaseHas('voitures', [
            'immatriculation' => 'AB-123-CD',
            'marque' => 'Peugeot',
            'modele' => '308',
            'kilometrage' => 30000,
            'statut' => 'disponible',
        ]);
    }

    public function test_les_champs_obligatoires_d_une_voiture_sont_valides(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->postJson('/voitures', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'immatriculation',
                'marque',
                'modele',
            ]);
    }

    public function test_l_immatriculation_doit_etre_unique(): void
    {
        $acteur = $this->createUser();

        $this->createVoiture([
            'immatriculation' => 'AA-111-AA',
            'statut' => 'disponible',
        ]);

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'immatriculation' => 'AA-111-AA',
                'marque' => 'Citroën',
                'modele' => 'C3',
                'kilometrage' => 10000,
                'statut' => 'disponible',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('immatriculation');
    }

    public function test_le_kilometrage_ne_peut_pas_etre_negatif(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'immatriculation' => 'ZZ-999-ZZ',
                'marque' => 'Renault',
                'modele' => 'Clio',
                'kilometrage' => -1,
                'statut' => 'disponible',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('kilometrage');
    }

    public function test_le_kilometrage_est_initialise_a_zero_lorsqu_il_est_omis(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'immatriculation' => 'CC-222-DD',
                'marque' => 'Toyota',
                'modele' => 'Yaris',
                'statut' => 'indisponible',
            ])
            ->assertOk();

        $this->assertDatabaseHas('voitures', [
            'immatriculation' => 'CC-222-DD',
            'kilometrage' => 0,
            'statut' => 'indisponible',
        ]);
    }

    public function test_une_voiture_peut_etre_modifiee(): void
    {
        $acteur = $this->createUser();

        $voiture = $this->createVoiture([
            'immatriculation' => 'EE-333-FF',
            'marque' => 'Renault',
            'modele' => 'Clio',
            'kilometrage' => 20000,
            'statut' => 'disponible',
        ]);

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'id' => $voiture->id,
                'immatriculation' => 'EE-333-FF',
                'marque' => 'Renault',
                'modele' => 'Captur',
                'kilometrage' => 40000,
                'statut' => 'réservé',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('voiture.modele', 'Captur')
            ->assertJsonPath('voiture.statut', 'réservé');

        $this->assertDatabaseHas('voitures', [
            'id' => $voiture->id,
            'immatriculation' => 'EE-333-FF',
            'modele' => 'Captur',
            'kilometrage' => 40000,
            'statut' => 'réservé',
        ]);
    }

    public function test_un_identifiant_de_voiture_inconnu_est_refuse(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'id' => 999999,
                'immatriculation' => 'GG-444-HH',
                'marque' => 'Ford',
                'modele' => 'Focus',
                'kilometrage' => 15000,
                'statut' => 'disponible',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('id');
    }

    public function test_un_statut_de_voiture_invalide_est_refuse(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'immatriculation' => 'HH-555-II',
                'marque' => 'Volkswagen',
                'modele' => 'Golf',
                'kilometrage' => 25000,
                'statut' => 'en panne',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('statut');
    }

    public function test_le_statut_reserve_est_accepte(): void
    {
        $acteur = $this->createUser();

        $this->actingAs($acteur)
            ->postJson('/voitures', [
                'immatriculation' => 'JJ-666-KK',
                'marque' => 'Peugeot',
                'modele' => '208',
                'kilometrage' => 18000,
                'statut' => 'réservé',
            ])
            ->assertOk();

        $this->assertDatabaseHas('voitures', [
            'immatriculation' => 'JJ-666-KK',
            'statut' => 'réservé',
        ]);
    }
}