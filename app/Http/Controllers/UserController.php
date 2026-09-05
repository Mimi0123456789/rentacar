<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index(): View
    {
        $users = User::query()
            ->orderBy('name')
            ->orderBy('login')
            ->get();

        return view('utilisateurs.index', [
            'users' => $users,
        ]);
    }

    /**
     * Crée un utilisateur.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'login' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'login'),
            ],

            'droit' => [
                'required',
                Rule::in([
                    'ADMIN',
                    'EMPLOYE',
                    'CLIENT',
                ]),
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        User::create([
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'],
            'login' => $validated['login'],
            'droit' => $validated['droit'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Modifie un utilisateur.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],

            'login' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'login')->ignore($user),
            ],

            'droit' => [
                'required',
                Rule::in([
                    'ADMIN',
                    'EMPLOYE',
                    'CLIENT',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        $data = [
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'],
            'login' => $validated['login'],
            'droit' => $validated['droit'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}