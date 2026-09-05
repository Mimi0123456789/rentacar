<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesRentacarData;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRentacarData;

    public function test_un_utilisateur_authentifie_peut_consulter_la_page_des_utilisateurs(): void
    {
        $actor = $this->createUser();

        $this->actingAs($actor)
            ->get('/utilisateurs')
            ->assertOk()
            ->assertViewIs('utilisateurs.index')
            ->assertViewHas('users');
    }

    public function test_un_utilisateur_peut_etre_cree(): void
    {
        $actor = $this->createUser();

        $response = $this->actingAs($actor)->post('/utilisateurs', [
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
            'login' => 'amartin',
            'droit' => 'EMPLOYE',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('utilisateurs.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'login' => 'amartin',
            'droit' => 'EMPLOYE',
        ]);

        $created = User::where('email', 'alice@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $created->password));
    }

    public function test_les_champs_obligatoires_d_un_utilisateur_sont_valides(): void
    {
        $actor = $this->createUser();

        $this->actingAs($actor)
            ->post('/utilisateurs', [])
            ->assertSessionHasErrors(['email', 'login', 'droit', 'password']);
    }

    public function test_l_adresse_email_doit_etre_valide(): void
    {
        $actor = $this->createUser();

        $this->actingAs($actor)->post('/utilisateurs', [
            'email' => 'pas-un-email',
            'login' => 'testuser',
            'droit' => 'EMPLOYE',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertSessionHasErrors('email');
    }

    public function test_l_adresse_email_doit_etre_unique(): void
    {
        $actor = $this->createUser(['email' => 'duplicate@example.com']);

        $this->actingAs($actor)->post('/utilisateurs', [
            'email' => 'duplicate@example.com',
            'login' => 'autrelogin',
            'droit' => 'EMPLOYE',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertSessionHasErrors('email');
    }

    public function test_le_login_doit_etre_unique(): void
    {
        $actor = $this->createUser(['login' => 'duplicate']);

        $this->actingAs($actor)->post('/utilisateurs', [
            'email' => 'other@example.com',
            'login' => 'duplicate',
            'droit' => 'EMPLOYE',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertSessionHasErrors('login');
    }

    public function test_le_droit_doit_correspondre_a_une_valeur_autorisee_par_le_controleur(): void
    {
        $actor = $this->createUser();

        $this->actingAs($actor)->post('/utilisateurs', [
            'email' => 'role@example.com',
            'login' => 'role_test',
            'droit' => 'SUPERADMIN',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertSessionHasErrors('droit');
    }

    public function test_le_mot_de_passe_doit_etre_confirme(): void
    {
        $actor = $this->createUser();

        $this->actingAs($actor)->post('/utilisateurs', [
            'email' => 'password@example.com',
            'login' => 'password_test',
            'droit' => 'EMPLOYE',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');
    }

    public function test_un_utilisateur_peut_etre_modifie_sans_changer_son_mot_de_passe(): void
    {
        $actor = $this->createUser();

        $user = $this->createUser([
            'email' => 'before@example.com',
            'login' => 'before',
            'password' => 'oldpassword',
        ]);

        $oldPassword = $user->password;

        $this->actingAs($actor)->put('/utilisateurs/' . $user->id, [
            'name' => 'Nom Modifié',
            'email' => 'after@example.com',
            'login' => 'after',
            'droit' => 'CLIENT',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('utilisateurs.index'));

        $user->refresh();

        $this->assertSame('Nom Modifié', $user->name);
        $this->assertSame('after@example.com', $user->email);
        $this->assertSame('after', $user->login);
        $this->assertSame('CLIENT', $user->droit);
        $this->assertSame($oldPassword, $user->password);
    }

    public function test_le_mot_de_passe_d_un_utilisateur_peut_etre_modifie(): void
    {
        $actor = $this->createUser();

        $user = $this->createUser([
            'password' => 'oldpassword'
        ]);

        $this->actingAs($actor)->put('/utilisateurs/' . $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'login' => $user->login,
            'droit' => 'ADMIN',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])->assertRedirect(route('utilisateurs.index'));

        $this->assertTrue(
            Hash::check('newpassword', $user->fresh()->password)
        );
    }

    public function test_un_utilisateur_peut_etre_supprime(): void
    {
        $actor = $this->createUser();
        $user = $this->createUser();

        $this->actingAs($actor)
            ->delete('/utilisateurs/' . $user->id)
            ->assertRedirect(route('utilisateurs.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}