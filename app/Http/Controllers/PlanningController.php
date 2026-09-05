<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Voiture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlanningController extends Controller
{
    public function index(): View
    {
        $tabs = [
            Reservation::STATUT_EN_ATTENTE => 'En attente',
            Reservation::STATUT_VALIDEE => 'Validée',
            Reservation::STATUT_TERMINEE => 'Terminée',
            Reservation::STATUT_ANNULEE => 'Annulée',
        ];

        $reservationsByStatus = [];

        foreach ($tabs as $status => $label) {
            $reservationsByStatus[$status] = Reservation::query()
                ->with([
                    'user',
                    'voiture',
                ])
                ->where('statut', $status)
                ->orderBy('date_debut')
                ->limit(25)
                ->get();
        }

        $counts = [
            Reservation::STATUT_EN_ATTENTE => Reservation::query()->where('statut', Reservation::STATUT_EN_ATTENTE)->count(),
            Reservation::STATUT_VALIDEE => Reservation::query()->where('statut', Reservation::STATUT_VALIDEE)->count(),
            Reservation::STATUT_TERMINEE => Reservation::query()->where('statut', Reservation::STATUT_TERMINEE)->count(),
            Reservation::STATUT_ANNULEE => Reservation::query()->where('statut', Reservation::STATUT_ANNULEE)->count(),
        ];

        $schedulerResources = Voiture::query()
            ->select(['id', 'immatriculation', 'marque', 'modele'])
            ->where('statut', '!=', 'indisponible')
            ->orderBy('marque')
            ->orderBy('modele')
            ->get()
            ->map(function (Voiture $voiture): array {
                return [
                    'id' => (string) $voiture->id,
                    'name' => trim($voiture->marque . ' ' . $voiture->modele . ' - ' . $voiture->immatriculation),
                ];
            })
            ->values()
            ->all();

        $schedulerEvents = Reservation::query()
            ->with('user', 'voiture')
            ->whereNotNull('voiture_id')
            ->whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->whereHas('voiture', function ($query) {
                $query->where('statut', '!=', 'indisponible');
            })
            ->orderBy('date_debut')
            ->get()
            ->map(function (Reservation $reservation): array {
                $statusColor = match ($reservation->statut) {
                    Reservation::STATUT_EN_ATTENTE => '#f59e0b',
                    Reservation::STATUT_VALIDEE => '#198754',
                    Reservation::STATUT_TERMINEE => '#6c757d',
                    Reservation::STATUT_ANNULEE => '#dc3545',
                    default => '#0d6efd',
                };

                return [
                    'id' => (string) $reservation->id,
                    'resource' => (string) $reservation->voiture_id,
                    'start' => $reservation->date_debut?->format('Y-m-d\TH:i:s'),
                    'end' => $reservation->date_fin?->format('Y-m-d\TH:i:s'),
                    'text' => $reservation->user?->name ?? 'Réservation',
                    'backColor' => $statusColor,
                    'borderColor' => $statusColor,
                ];
            })
            ->values()
            ->all();

        return view('planning.index', [
            'tabs' => $tabs,
            'reservationsByStatus' => $reservationsByStatus,
            'counts' => $counts,
            'schedulerResources' => $schedulerResources,
            'schedulerEvents' => $schedulerEvents,
        ]);
    }

    public function edit(Reservation $reservation): JsonResponse
    {
        $reservation->load(['user', 'voiture']);

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'user_name' => $reservation->user?->name ?? 'Utilisateur inconnu',
                'user_email' => $reservation->user?->email ?? null,
                'date_debut' => $reservation->date_debut?->format('Y-m-d\TH:i'),
                'date_fin' => $reservation->date_fin?->format('Y-m-d\TH:i'),
                'motif' => $reservation->motif,
                'nb_passagers' => $reservation->nb_passagers,
                'bagages' => (bool) $reservation->bagages,
                'voiture_id' => $reservation->voiture_id,
                'statut' => $reservation->statut,
            ],
            'available_vehicles' => $this->availableVehiclesForReservation($reservation)->map(function (Voiture $voiture): array {
                return [
                    'id' => (string) $voiture->id,
                    'label' => trim($voiture->marque . ' ' . $voiture->modele . ' - ' . $voiture->immatriculation),
                ];
            })->values()->all(),
            'statuses' => [
                Reservation::STATUT_EN_ATTENTE => 'En attente',
                Reservation::STATUT_VALIDEE => 'Validée',
                Reservation::STATUT_TERMINEE => 'Terminée',
                Reservation::STATUT_ANNULEE => 'Annulée',
            ],
            'update_url' => route('planning.reservations.update', ['reservation' => $reservation->id]),
        ]);
    }

    public function update(Request $request, Reservation $reservation): JsonResponse
    {
        $validated = $request->validate([
            'date_debut' => ['required', 'date_format:Y-m-d\TH:i'],
            'date_fin' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:date_debut'],
            'motif' => ['nullable', 'string'],
            'nb_passagers' => ['nullable', 'integer', 'min:1'],
            'bagages' => ['nullable', 'boolean'],
            'voiture_id' => ['nullable', 'exists:voitures,id'],
            'statut' => ['required', Rule::in([
                Reservation::STATUT_EN_ATTENTE,
                Reservation::STATUT_VALIDEE,
                Reservation::STATUT_TERMINEE,
                Reservation::STATUT_ANNULEE,
            ])],
        ]);

        $reservation->date_debut = $validated['date_debut'];
        $reservation->date_fin = $validated['date_fin'];
        $reservation->motif = $validated['motif'] ?? null;
        $reservation->nb_passagers = $validated['nb_passagers'] ?? null;
        $reservation->bagages = (bool) $request->boolean('bagages');

        $availableVehicles = $this->availableVehiclesForReservation($reservation);
        $selectedVehicle = null;

        if (! empty($validated['voiture_id'])) {
            $selectedVehicle = $availableVehicles->firstWhere('id', (int) $validated['voiture_id']);

            if ($selectedVehicle === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le véhicule sélectionné n’est pas disponible sur les dates demandées.',
                ], 422);
            }

            $reservation->voiture_id = $validated['voiture_id'];
        }

        $reservation->statut = $validated['statut'];
        $reservation->save();

        $this->sendStatusNotificationEmail($reservation);

        return response()->json([
            'success' => true,
            'message' => 'La demande a bien été mise à jour.',
            'reservation' => [
                'id' => $reservation->id,
                'voiture_id' => $reservation->voiture_id,
                'statut' => $reservation->statut,
            ],
        ]);
    }

    private function sendStatusNotificationEmail(Reservation $reservation): void
    {
        $reservation->loadMissing('user');

        if (! $reservation->user?->email) {
            return;
        }

        $mailView = match ($reservation->statut) {
            Reservation::STATUT_EN_ATTENTE => 'mails.confirmation',
            Reservation::STATUT_VALIDEE, Reservation::STATUT_TERMINEE => 'mails.valide',
            Reservation::STATUT_ANNULEE => 'mails.annulation',
            default => null,
        };

        if (! $mailView) {
            return;
        }

        $subject = match ($reservation->statut) {
            Reservation::STATUT_EN_ATTENTE => 'Votre demande a bien été prise en compte',
            Reservation::STATUT_VALIDEE => 'Votre réservation a été validée',
            Reservation::STATUT_TERMINEE => 'Votre réservation est terminée',
            Reservation::STATUT_ANNULEE => 'Votre réservation a été annulée',
            default => 'Mise à jour de votre réservation',
        };

        Mail::send($mailView, ['reservation' => $reservation], function ($message) use ($reservation, $subject) {
            $message->to($reservation->user->email)
                ->subject($subject);
        });
    }

    private function availableVehiclesForReservation(Reservation $reservation)
    {
        if (! $reservation->date_debut || ! $reservation->date_fin) {
            return Voiture::query()
                ->where('statut', '!=', 'indisponible')
                ->orderBy('marque')
                ->orderBy('modele')
                ->get();
        }

        return Voiture::query()
            ->where('statut', '!=', 'indisponible')
            ->whereDoesntHave('reservations', function ($query) use ($reservation) {
                $query
                    ->where('id', '!=', $reservation->id)
                    ->where('date_debut', '<', $reservation->date_fin)
                    ->where('date_fin', '>', $reservation->date_debut)
                    ->whereNotIn('statut', [Reservation::STATUT_ANNULEE]);
            })
            ->orderBy('marque')
            ->orderBy('modele')
            ->get();
    }
}