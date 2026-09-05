<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CollaborateurPlanningController extends Controller
{
    /**
     * Affiche uniquement les réservations
     * du collaborateur connecté.
     */
    public function index(): View
    {
        $reservations = Reservation::query()
            ->with('voiture')
            ->where('user_id', Auth::id())
            ->whereIn('statut', [
                Reservation::EN_ATTENTE,
                Reservation::VALIDEE,
                Reservation::TERMINEE,
            ])
            ->orderBy('date_debut')
            ->get();

        return view('collaborateur.planning', [
            'reservations' => $reservations,
        ]);
    }
}