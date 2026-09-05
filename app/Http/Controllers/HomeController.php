<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirige l'utilisateur vers l'espace adapté à son rôle.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isCollaborateur()) {
            return redirect()->route('collaborateur.dashboard');
        }

        if ($user->isGestion() || $user->isAdmin()) {
            return redirect()->route('voitures.index');
        }

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'login' => 'Votre compte ne possède aucun rôle valide.',
            ]);
    }
}