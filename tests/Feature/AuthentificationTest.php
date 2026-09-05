<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesRentacarData;
use Tests\TestCase;

class AuthentificationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRentacarData;

    public function test_la_racine_redirige_vers_la_page_de_connexion(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }

    public function test_un_visiteur_ne_peut_pas_acceder_a_la_gestion_des_utilisateurs(): void
    {
        $this->get(route('utilisateurs.index'))
            ->assertRedirect(route('login'));
    }

    public function test_un_visiteur_ne_peut_pas_acceder_a_la_gestion_des_voitures(): void
    {
        $this->get(route('voitures.index'))
            ->assertRedirect(route('login'));
    }

    public function test_un_visiteur_ne_peut_pas_acceder_au_planning(): void
    {
        $this->get(route('planning.index'))
            ->assertRedirect(route('login'));
    }

    public function test_un_utilisateur_peut_se_connecter_avec_des_identifiants_valides(): void
    {
        $utilisateur = $this->createUser([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $reponse = $this->post(route('login'), [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $reponse->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($utilisateur);

        $reponse->assertRedirect('/planning');
    }

    public function test_un_utilisateur_ne_peut_pas_se_connecter_avec_un_mot_de_passe_incorrect(): void
    {
        $this->createUser([
            'login' => 'testconnexion',
            'password' => 'password123',
        ]);

        $this->post(route('login'), [
            'login' => 'testconnexion',
            'password' => 'mot-de-passe-incorrect',
        ]);

        $this->assertGuest();
    }

    public function test_un_utilisateur_ne_peut_pas_se_connecter_avec_un_login_inconnu(): void
    {
        $this->post(route('login'), [
            'login' => 'utilisateur-inconnu',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    public function test_un_utilisateur_authentifie_peut_se_deconnecter(): void
    {
        $utilisateur = $this->createUser();

        $this->actingAs($utilisateur);

        $this->assertAuthenticatedAs($utilisateur);

        $reponse = $this->post(route('logout'));

        $reponse->assertRedirect('/');

        $this->assertGuest();
    }
}