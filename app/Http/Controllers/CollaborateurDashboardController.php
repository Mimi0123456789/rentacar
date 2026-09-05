<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CollaborateurDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du collaborateur.
     */
    public function index(): View
    {
        $user = Auth::user();
        $now = now();

        $demandesEnCours = Reservation::query()
            ->where('user_id', $user->id)
            ->where('statut', Reservation::STATUT_EN_ATTENTE)
            ->orderByDesc('created_at')
            ->get();

        $reservationsActuelles = Reservation::query()
            ->with('voiture')
            ->where('user_id', $user->id)
            ->where('statut', Reservation::STATUT_VALIDEE)
            ->whereNotNull('voiture_id')
            ->where('date_debut', '<=', $now)
            ->where('date_fin', '>=', $now)
            ->orderBy('date_fin')
            ->get();

        $reservationsAVenir = Reservation::query()
            ->with('voiture')
            ->where('user_id', $user->id)
            ->where('statut', Reservation::STATUT_VALIDEE)
            ->whereNotNull('voiture_id')
            ->where('date_debut', '>', $now)
            ->orderBy('date_debut')
            ->get();

        $historique = Reservation::query()
            ->with('voiture')
            ->where('user_id', $user->id)
            ->whereNotNull('voiture_id')
            ->where(function ($query) use ($now) {
                $query
                    ->where('statut', Reservation::STATUT_TERMINEE)
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery
                            ->where('statut', Reservation::STATUT_VALIDEE)
                            ->where('date_fin', '<', $now);
                    });
            })
            ->orderByDesc('date_fin')
            ->limit(10)
            ->get();

        return view('collaborateurs.dashboard', [
            'user' => $user,
            'demandesEnCours' => $demandesEnCours,
            'reservationsActuelles' => $reservationsActuelles,
            'reservationsAVenir' => $reservationsAVenir,
            'historique' => $historique,
        ]);
    }

    /**
     * Retourne les données d’une demande à modifier.
     */
    public function edit(Reservation $reservation): JsonResponse
    {
        try {
            $authorizationError = $this->checkEditableReservation(
                $reservation
            );

            if ($authorizationError !== null) {
                return $authorizationError;
            }

            return response()->json([
                'success' => true,

                'reservation' => [
                    'id' => $reservation->id,

                    'date_debut' => $reservation->date_debut
                        ?->format('Y-m-d\TH:i'),

                    'date_fin' => $reservation->date_fin
                        ?->format('Y-m-d\TH:i'),

                    'motif' => $reservation->motif,

                    'nb_passagers' => $reservation->nb_passagers,

                    'bagages' => (bool) $reservation->bagages,
                ],

                'update_url' => route(
                    'collaborateur.demandes.update',
                    ['reservation' => $reservation->id]
                ),
            ]);

        } catch (\Throwable $exception) {
            Log::error(
                'Erreur lors de la récupération de la demande',
                [
                    'reservation_id' => $reservation->id ?? null,
                    'user_id' => Auth::id(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Impossible de récupérer cette demande.',
            ], 500);
        }
    }

    /**
     * Modifie une demande de réservation en attente.
     */
    public function update(
        Request $request,
        Reservation $reservation
    ): JsonResponse {
        try {
            $authorizationError = $this->checkEditableReservation(
                $reservation
            );

            if ($authorizationError !== null) {
                return $authorizationError;
            }

            $validated = $request->validate([
                'date_debut' => [
                    'required',
                    'date',
                    'after_or_equal:now',
                ],

                'date_fin' => [
                    'required',
                    'date',
                    'after:date_debut',
                ],

                'motif' => [
                    'required',
                    'string',
                    'max:1000',
                ],

                'nb_passagers' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],

                'bagages' => [
                    'required',
                    'boolean',
                ],
            ], [
                'date_debut.required' =>
                    'La date de départ est obligatoire.',

                'date_debut.date' =>
                    'La date de départ est invalide.',

                'date_debut.after_or_equal' =>
                    'La date de départ ne peut pas être passée.',

                'date_fin.required' =>
                    'La date de retour est obligatoire.',

                'date_fin.date' =>
                    'La date de retour est invalide.',

                'date_fin.after' =>
                    'La date de retour doit être postérieure à la date de départ.',

                'motif.required' =>
                    'Le motif du déplacement est obligatoire.',

                'motif.max' =>
                    'Le motif ne peut pas dépasser 1 000 caractères.',

                'nb_passagers.required' =>
                    'Le nombre de passagers est obligatoire.',

                'nb_passagers.integer' =>
                    'Le nombre de passagers doit être un nombre entier.',

                'nb_passagers.min' =>
                    'Le nombre de passagers doit être au minimum de 1.',

                'nb_passagers.max' =>
                    'Le nombre de passagers ne peut pas dépasser 20.',

                'bagages.required' =>
                    'Veuillez préciser si vous transportez des bagages.',
            ]);

            $reservation->date_debut = $validated['date_debut'];
            $reservation->date_fin = $validated['date_fin'];
            $reservation->motif = $validated['motif'];
            $reservation->nb_passagers = $validated['nb_passagers'];
            $reservation->bagages = (bool) $validated['bagages'];

            $reservation->save();

            $this->sendConfirmationEmail($reservation);

            return response()->json([
                'success' => true,
                'message' => 'Votre demande a bien été modifiée.',
                'reservation' => [
                    'id' => $reservation->id,

                    'date_debut' => $reservation->date_debut
                        ?->format('d/m/Y à H:i'),

                    'date_fin' => $reservation->date_fin
                        ?->format('d/m/Y à H:i'),

                    'motif' => $reservation->motif,

                    'nb_passagers' => $reservation->nb_passagers,

                    'bagages' => (bool) $reservation->bagages,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;

        } catch (\Throwable $exception) {
            Log::error(
                'Erreur lors de la modification de la réservation',
                [
                    'reservation_id' => $reservation->id ?? null,
                    'user_id' => Auth::id(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Une erreur est survenue lors de la modification.',
            ], 500);
        }
    }

    /**
     * Annule une demande de réservation en attente.
     */
    public function destroy(
        Reservation $reservation
    ): JsonResponse {
        try {
            $authorizationError = $this->checkEditableReservation(
                $reservation
            );

            if ($authorizationError !== null) {
                return $authorizationError;
            }

            $reservation->statut = Reservation::STATUT_ANNULEE;
            $reservation->save();

            return response()->json([
                'success' => true,
                'message' => 'Votre demande a bien été annulée.',
            ]);

        } catch (\Throwable $exception) {
            Log::error(
                'Erreur lors de l’annulation de la réservation',
                [
                    'reservation_id' => $reservation->id ?? null,
                    'user_id' => Auth::id(),
                    'statut_actuel' => $reservation->statut ?? null,
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Une erreur est survenue lors de l’annulation.',
            ], 500);
        }
    }

    /**
     * Vérifie que la demande appartient au collaborateur connecté
     * et qu’elle est encore en attente.
     */
    private function sendConfirmationEmail(Reservation $reservation): void
    {
        $reservation->loadMissing('user');

        if (! $reservation->user?->email) {
            return;
        }

        Mail::send('mails.confirmation', ['reservation' => $reservation], function ($message) use ($reservation) {
            $message->to($reservation->user->email)
                ->subject('Votre demande de réservation a été mise à jour');
        });
    }

    private function checkEditableReservation(
        Reservation $reservation
    ): ?JsonResponse {
        if ((int) $reservation->user_id !== (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Vous ne pouvez pas modifier ou annuler cette demande.',
            ], 403);
        }

        if ($reservation->statut !== Reservation::STATUT_EN_ATTENTE) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Cette demande a déjà été traitée et ne peut plus être modifiée.',
            ], 422);
        }

        return null;
    }
}