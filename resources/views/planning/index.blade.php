@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-calendar3 me-2 text-primary"></i>
                Planning des réservations
            </h1>

            <p class="text-muted mb-0">
                Consultation et gestion des demandes de réservation de véhicules.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">

        @foreach($tabs as $status => $label)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">{{ $label }}</div>
                        <div class="fs-3 fw-bold {{ $status === \App\Models\Reservation::STATUT_EN_ATTENTE ? 'text-warning' : ($status === \App\Models\Reservation::STATUT_VALIDEE ? 'text-success' : ($status === \App\Models\Reservation::STATUT_TERMINEE ? 'text-secondary' : 'text-danger')) }}">
                            {{ $counts[$status] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h5 mb-0">
                    <i class="bi bi-calendar-week me-2"></i>
                    Calendrier des réservations
                </h2>

                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#planningFilterModal">
                    <i class="bi bi-funnel me-1"></i>
                    Filtrer le planning
                </button>
            </div>
        </div>

        <div class="card-body">
            <div id="scheduler" style="height: 720px;"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h5 mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Demandes de réservation
                </h2>

                <span class="badge bg-secondary">Affichage limité à 25 par onglet</span>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="reservationStatusTabs" role="tablist">
                @foreach($tabs as $status => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="tab-{{ strtolower($status) }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-{{ strtolower($status) }}"
                                type="button"
                                role="tab"
                                aria-controls="tab-{{ strtolower($status) }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $label }}
                            <span class="badge text-bg-light ms-1">{{ $counts[$status] ?? 0 }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($tabs as $status => $label)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="tab-{{ strtolower($status) }}"
                         role="tabpanel"
                         aria-labelledby="tab-{{ strtolower($status) }}-tab">

                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0" id="reservationsTable-{{ strtolower($status) }}">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">N°</th>
                                        <th scope="col">Demandeur</th>
                                        <th scope="col">Véhicule</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Durée</th>
                                        <th scope="col">Motif</th>
                                        <th scope="col" class="text-center">Passagers</th>
                                        <th scope="col" class="text-center">Bagages</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($reservationsByStatus[$status] as $reservation)
                                        @php
                                            $statut = strtolower($reservation->statut);

                                            $badgeClass = match ($statut) {
                                                'validee', 'validée', 'acceptee', 'acceptée' => 'bg-success',
                                                'annulee', 'annulée', 'refusee', 'refusée' => 'bg-danger',
                                                'en cours' => 'bg-primary',
                                                'terminee', 'terminée' => 'bg-secondary',
                                                default => 'bg-warning text-dark',
                                            };

                                            $dateDebut = $reservation->date_debut;
                                            $dateFin = $reservation->date_fin;

                                            $duree = $dateDebut && $dateFin
                                                ? $dateDebut->diffForHumans($dateFin, [
                                                    'parts' => 2,
                                                    'short' => true,
                                                    'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                                                ])
                                                : null;
                                        @endphp

                                        <tr data-reservation-id="{{ $reservation->id }}"
                                            data-user-id="{{ $reservation->user_id }}"
                                            data-voiture-id="{{ $reservation->voiture_id }}"
                                            data-date-debut="{{ optional($dateDebut)->format('Y-m-d\TH:i:s') }}"
                                            data-date-fin="{{ optional($dateFin)->format('Y-m-d\TH:i:s') }}"
                                            data-statut="{{ $reservation->statut }}">
                                            <td class="fw-semibold">#{{ $reservation->id }}</td>

                                            <td>
                                                <div class="fw-semibold">{{ $reservation->user->name ?? 'Utilisateur inconnu' }}</div>
                                                @if($reservation->user?->email)
                                                    <div class="small text-muted">{{ $reservation->user->email }}</div>
                                                @endif
                                            </td>

                                            <td>
                                                @if($reservation->voiture)
                                                    <div class="fw-semibold">
                                                        {{ $reservation->voiture->marque }}
                                                        {{ $reservation->voiture->modele }}
                                                    </div>
                                                    <div class="small text-muted">{{ $reservation->voiture->immatriculation }}</div>
                                                @else
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="bi bi-hourglass-split me-1"></i>
                                                        Non attribué
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="fw-semibold">{{ optional($dateDebut)->format('d/m/Y') }}</div>
                                                <div class="small text-muted">{{ optional($dateDebut)->format('H:i') }}</div>
                                            </td>

                                            <td>
                                                <div class="fw-semibold">{{ optional($dateFin)->format('d/m/Y') }}</div>
                                                <div class="small text-muted">{{ optional($dateFin)->format('H:i') }}</div>
                                            </td>

                                            <td>{{ $duree ?? '—' }}</td>

                                            <td style="min-width: 180px;">
                                                {{ \Illuminate\Support\Str::limit($reservation->motif ?? 'Non renseigné', 60) }}
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ $reservation->nb_passagers }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                @if($reservation->bagages)
                                                    <span class="badge bg-info text-dark">
                                                        <i class="bi bi-suitcase-lg me-1"></i>
                                                        Oui
                                                    </span>
                                                @else
                                                    <span class="text-muted">Non</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($reservation->statut) }}
                                                </span>
                                            </td>

                                            <td class="text-end">
                                                <div class="btn-group" role="group" aria-label="Actions réservation">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="Gérer la demande"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reservationEditModal"
                                                            data-reservation-id="{{ $reservation->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-5">
                                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                                <p class="fw-semibold mt-3 mb-1">Aucune réservation</p>
                                                <p class="text-muted mb-3">Aucune demande de réservation n'a encore été enregistrée dans cette catégorie.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="planningFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtrer le planning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="filter_date_start" class="form-label">Date de début</label>
                        <input type="date" id="filter_date_start" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label for="filter_date_end" class="form-label">Date de fin</label>
                        <input type="date" id="filter_date_end" class="form-control">
                    </div>

                    <div class="col-12">
                        <label for="filter_vehicles" class="form-label">Véhicules</label>
                        <select id="filter_vehicles" class="form-select" multiple size="6">
                            @foreach($schedulerResources as $resource)
                                <option value="{{ $resource['id'] }}">{{ $resource['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="reset_planning_filters" class="btn btn-outline-secondary">Réinitialiser</button>
                <button type="button" id="apply_planning_filters" class="btn btn-primary">Appliquer</button>
            </div>
        </div>
    </div>
</div>

<-- FENETRE MODALE -->
<div class="modal fade" id="reservationEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gérer la demande de réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <form id="reservationEditForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Demandeur</label>
                            <input type="text" class="form-control" id="edit_user_name" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" id="edit_user_email" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Début</label>
                            <input type="datetime-local" class="form-control" id="edit_date_debut" name="date_debut">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fin</label>
                            <input type="datetime-local" class="form-control" id="edit_date_fin" name="date_fin">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Motif</label>
                            <textarea class="form-control" id="edit_motif" name="motif" rows="3"></textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Passagers</label>
                            <input type="number" class="form-control" id="edit_nb_passagers" name="nb_passagers">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Bagages</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="edit_bagages" name="bagages" value="1">
                                <label class="form-check-label" for="edit_bagages">Oui</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Véhicule disponible</label>
                            <select class="form-select" id="edit_voiture_id" name="voiture_id"></select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="edit_statut" name="statut"></select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('daypilot-pro-javascript/daypilot-javascript.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const schedulerContainer = document.getElementById('scheduler');
            const resources = @json($schedulerResources ?? []);
            const events = @json($schedulerEvents ?? []);
            const reservationEditModal = document.getElementById('reservationEditModal');
            const reservationEditForm = document.getElementById('reservationEditForm');
            const filterDateStart = document.getElementById('filter_date_start');
            const filterDateEnd = document.getElementById('filter_date_end');
            const filterVehicles = document.getElementById('filter_vehicles');
            const applyFiltersButton = document.getElementById('apply_planning_filters');
            const resetFiltersButton = document.getElementById('reset_planning_filters');
            const editUserName = document.getElementById('edit_user_name');
            const editUserEmail = document.getElementById('edit_user_email');
            const editDateDebut = document.getElementById('edit_date_debut');
            const editDateFin = document.getElementById('edit_date_fin');
            const editMotif = document.getElementById('edit_motif');
            const editNbPassagers = document.getElementById('edit_nb_passagers');
            const editBagages = document.getElementById('edit_bagages');
            const editVoitureId = document.getElementById('edit_voiture_id');
            const editStatut = document.getElementById('edit_statut');

            if (!schedulerContainer) {
                return;
            }

            if (typeof DayPilot === 'undefined') {
                schedulerContainer.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-3 mb-0">DayPilot n’a pas pu être chargé.</p></div>';
                return;
            }

            if (!resources.length) {
                schedulerContainer.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-car-front fs-1"></i><p class="mt-3 mb-0">Aucune voiture disponible pour afficher le planning.</p></div>';
                return;
            }

            const allResources = resources;
            const allEvents = events;
            let dp;

            function createScheduler() {
                dp = new DayPilot.Scheduler('scheduler', {
                    locale: 'fr-fr',
                    startDate: DayPilot.Date.today().addDays(-7),
                    days: 60,
                    scale: 'Day',
                    timeHeaders: [
                        { groupBy: 'Month' },
                        { groupBy: 'Day', format: 'd' }
                    ],
                    rowHeaderWidth: 280,
                    cellWidth: 70,
                    eventHeight: 38,
                    eventMoveHandling: 'Disabled',
                    eventResizeHandling: 'Disabled',
                    eventClickHandling: 'Disabled',
                    allowEventOverlap: false,
                    resources: allResources,
                    events: allEvents
                });

                dp.init();
            }

            function applySchedulerFilters() {
                if (!dp) {
                    return;
                }

                const selectedVehicleIds = Array.from(filterVehicles.selectedOptions).map((option) => option.value);
                const startFilter = filterDateStart.value ? new DayPilot.Date(filterDateStart.value + 'T00:00:00') : null;
                const endFilter = filterDateEnd.value ? new DayPilot.Date(filterDateEnd.value + 'T23:59:59') : null;

                const filteredResources = selectedVehicleIds.length
                    ? allResources.filter((resource) => selectedVehicleIds.includes(String(resource.id)))
                    : allResources;

                const filteredEvents = allEvents.filter((event) => {
                    if (selectedVehicleIds.length && !selectedVehicleIds.includes(String(event.resource))) {
                        return false;
                    }

                    const eventStart = event.start ? new DayPilot.Date(event.start) : null;
                    const eventEnd = event.end ? new DayPilot.Date(event.end) : null;

                    if (!eventStart || !eventEnd) {
                        return false;
                    }

                    if (startFilter && eventStart < startFilter) {
                        return false;
                    }

                    if (endFilter && eventEnd > endFilter) {
                        return false;
                    }

                    return true;
                });

                dp.update({
                    resources: filteredResources,
                    events: filteredEvents
                });
            }

            function resetSchedulerFilters() {
                filterDateStart.value = '';
                filterDateEnd.value = '';
                Array.from(filterVehicles.options).forEach((option) => option.selected = false);
                applySchedulerFilters();
            }

            createScheduler();
            applyFiltersButton.addEventListener('click', function () {
                applySchedulerFilters();
                bootstrap.Modal.getInstance(document.getElementById('planningFilterModal'))?.hide();
            });
            resetFiltersButton.addEventListener('click', function () {
                resetSchedulerFilters();
                bootstrap.Modal.getInstance(document.getElementById('planningFilterModal'))?.hide();
            });

            editVoitureId.addEventListener('change', function () {
                if (editVoitureId.value) {
                    editStatut.disabled = false;
                }
            });

            if (reservationEditModal) {
                reservationEditModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const reservationId = button?.getAttribute('data-reservation-id');

                    if (!reservationId) {
                        return;
                    }

                    fetch(`/planning/reservations/${reservationId}/edit`)
                        .then((response) => response.json())
                        .then((data) => {
                            if (!data.success) {
                                alert(data.message || 'Impossible de charger la demande.');
                                return;
                            }

                            editUserName.value = data.reservation.user_name ?? '';
                            editUserEmail.value = data.reservation.user_email ?? '';
                            editDateDebut.value = data.reservation.date_debut ?? '';
                            editDateFin.value = data.reservation.date_fin ?? '';
                            editMotif.value = data.reservation.motif ?? '';
                            editNbPassagers.value = data.reservation.nb_passagers ?? '';
                            editBagages.checked = Boolean(data.reservation.bagages);

                            editVoitureId.innerHTML = '';
                            if (Array.isArray(data.available_vehicles) && data.available_vehicles.length > 0) {
                                data.available_vehicles.forEach((vehicle) => {
                                    const option = document.createElement('option');
                                    option.value = vehicle.id;
                                    option.textContent = vehicle.label;
                                    if (String(data.reservation.voiture_id) === String(vehicle.id)) {
                                        option.selected = true;
                                    }
                                    editVoitureId.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'Aucun véhicule disponible';
                                editVoitureId.appendChild(option);
                            }

                            editStatut.innerHTML = '';
                            Object.entries(data.statuses || {}).forEach(([value, label]) => {
                                const option = document.createElement('option');
                                option.value = value;
                                option.textContent = label;
                                if (value === data.reservation.statut) {
                                    option.selected = true;
                                }
                                editStatut.appendChild(option);
                            });

                            editStatut.disabled = false;
                            reservationEditForm.setAttribute('action', data.update_url);
                        });
                });

                reservationEditForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const formData = new FormData(reservationEditForm);
                    const url = reservationEditForm.getAttribute('action');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (!data.success) {
                                alert(data.message || 'Erreur lors de la mise à jour.');
                                return;
                            }

                            bootstrap.Modal.getInstance(reservationEditModal)?.hide();
                            window.location.reload();
                        })
                        .catch(() => {
                            alert('Erreur lors de la mise à jour de la demande.');
                        });
                });
            }
        });
    </script>
@endpush
