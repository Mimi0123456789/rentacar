<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Support\CreatesRentacarData;
use Tests\TestCase;

class PlanningRoutesTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRentacarData;

    public function test_un_utilisateur_authentifie_peut_acceder_au_planning(): void
    {
        $utilisateur = $this->createUser();

        $this->actingAs($utilisateur)
            ->get(route('planning.index'))
            ->assertOk()
            ->assertViewIs('planning.index');
    }

    public function test_la_route_du_planning_est_enregistree(): void
    {
        $this->assertTrue(Route::has('planning.index'));
    }

    public function test_un_visiteur_ne_peut_pas_acceder_au_planning(): void
    {
        $this->get(route('planning.index'))
            ->assertRedirect(route('login'));
    }
}