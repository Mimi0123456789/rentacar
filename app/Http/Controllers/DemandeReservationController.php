<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class DemandeReservationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
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
                    'max:9',
                ],
                'bagages' => [
                    'nullable',
                    'boolean',
                ],
            ]);

            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'voiture_id' => null,
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'kilometrage_depart' => null,
                'kilometrage_retour' => null,
                'motif' => $validated['motif'],
                'nb_passagers' => $validated['nb_passagers'],
                'bagages' => $request->boolean('bagages'),
                'statut' => Reservation::STATUT_EN_ATTENTE,
            ]);

            $this->sendConfirmationEmail($reservation);

            return response()->json([
                'success' => true,
                'message' => 'Votre demande de réservation a été enregistrée.',
                'reservation' => $reservation,
            ], 201);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Erreur lors de la création de la réservation', [
                'user_id' => Auth::id(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Une erreur est survenue lors de la création de la demande.',
            ], 500);
        }
    }

    private function sendConfirmationEmail(Reservation $reservation): void
    {
        $reservation->loadMissing('user');

        if (! $reservation->user?->email) {
            return;
        }

        Mail::send('mails.confirmation', ['reservation' => $reservation], function ($message) use ($reservation) {
            $message->to($reservation->user->email)
                ->subject('Votre demande de réservation a été reçue');
        });
    }
}