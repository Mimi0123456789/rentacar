@extends('layouts.app')

@section('content')

<style>
    :root {
        --app-content-bg: #ffffff;
        --text-primary: #212529;
        --text-secondary: #6c757d;
        --border-color: #dee2e6;
        --border-color-light: #f1f3f5;

        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --shadow-lg: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);

        --color-ok: #198754;
        --color-ok-bg: #d1e7dd;

        --color-warning: #997404;
        --color-warning-bg: #fff3cd;

        --color-ko: #dc3545;
        --color-ko-bg: #f8d7da;
    }

    .voitures-page {
        width: 95%;
        margin: 2.5rem auto;
    }

    .voitures-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .voitures-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .voitures-subtitle {
        margin: 0.25rem 0 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 2rem;
    }

    .card-voiture {
        background-color: var(--app-content-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        transition:
            box-shadow 0.2s ease,
            transform 0.2s ease,
            border-color 0.2s ease;
    }

    .card-voiture:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        border-color: #adb5bd;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .card-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .card-subtitle {
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .card-section {
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--border-color-light);
        padding-bottom: 0.75rem;
    }

    .card-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .card-section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .etat {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 1rem;
        border: 1px solid transparent;
        min-width: 110px;
        text-align: center;
        white-space: nowrap;
    }

    .etat-disponible {
        color: var(--color-ok);
        background-color: var(--color-ok-bg);
        border-color: var(--color-ok);
    }

    .etat-reserve {
        color: var(--color-warning);
        background-color: var(--color-warning-bg);
        border-color: #ffca2c;
    }

    .etat-indisponible {
        color: var(--color-ko);
        background-color: var(--color-ko-bg);
        border-color: var(--color-ko);
    }

    .empty-state {
        grid-column: 1 / -1;
        padding: 3rem 1rem;
        text-align: center;
        color: var(--text-secondary);
        font-style: italic;
        background: #ffffff;
        border: 1px dashed var(--border-color);
        border-radius: 12px;
    }

    .form-section-title {
        margin-bottom: 1rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--border-color);
        color: #dc3545;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: 1px solid var(--border-color);
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
    }

    @media (max-width: 768px) {
        .voitures-page {
            width: 92%;
            margin-top: 1.5rem;
        }

        .voitures-header {
            align-items: stretch;
            flex-direction: column;
        }

        .voitures-header .btn {
            width: 100%;
        }

        .card-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="voitures-page">

    <div class="voitures-header">
        <div>
            <h1 class="voitures-title">
                <i class="bi bi-car-front-fill text-danger"></i>
                Listing des voitures
            </h1>

            <p class="voitures-subtitle">
                Gestion des véhicules disponibles à la réservation
            </p>
        </div>

        <button
            type="button"
            class="btn btn-danger rounded-pill px-4"
            onclick="openNewVoiture()"
        >
            <i class="bi bi-plus-circle"></i>
            Nouvelle voiture
        </button>
    </div>

    <div class="card-grid">

        @forelse($voitures as $voiture)

            @php
                $statutClass = match ($voiture->statut) {
                    'disponible' => 'etat-disponible',
                    'réservé' => 'etat-reserve',
                    'indisponible' => 'etat-indisponible',
                    default => 'etat-indisponible',
                };

                $statutIcon = match ($voiture->statut) {
                    'disponible' => 'bi-check-circle-fill',
                    'réservé' => 'bi-calendar-check-fill',
                    'indisponible' => 'bi-x-circle-fill',
                    default => 'bi-question-circle-fill',
                };
            @endphp

            <div
                class="card-voiture"
                ondblclick="openEditVoiture(this)"
                title="Double-cliquez pour modifier"

                data-id="{{ $voiture->id }}"
                data-immatriculation="{{ $voiture->immatriculation }}"
                data-marque="{{ $voiture->marque }}"
                data-modele="{{ $voiture->modele }}"
                data-kilometrage="{{ $voiture->kilometrage }}"
                data-statut="{{ $voiture->statut }}"
            >

                <div class="card-header">
                    <div>
                        <h2 class="card-title">
                            {{ $voiture->marque }} {{ $voiture->modele }}
                        </h2>

                        <div class="card-subtitle">
                            <i class="bi bi-credit-card-2-front"></i>
                            {{ $voiture->immatriculation }}
                        </div>
                    </div>

                    <span class="etat {{ $statutClass }}">
                        <i class="bi {{ $statutIcon }}"></i>
                        {{ ucfirst($voiture->statut) }}
                    </span>
                </div>

                <div class="card-section">
                    <div class="card-section-title">
                        Kilométrage
                    </div>

                    <div class="fw-semibold text-dark">
                        <i class="bi bi-speedometer2 text-secondary"></i>

                        {{ number_format(
                            $voiture->kilometrage,
                            0,
                            ',',
                            ' '
                        ) }} km
                    </div>
                </div>
            </div>

        @empty

            <div class="empty-state">
                <i class="bi bi-car-front fs-1 d-block mb-2"></i>
                Aucune voiture trouvée
            </div>

        @endforelse

    </div>
</div>

{{-- MODALE VOITURE --}}
<div
    class="modal fade"
    id="popupForm"
    tabindex="-1"
    aria-labelledby="popupFormTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">

            {{-- HEADER --}}
            <div class="modal-header bg-light">
                <h5
                    class="modal-title text-danger fw-bold"
                    id="popupFormTitle"
                >
                    <i class="bi bi-car-front-fill"></i>
                    <span id="modalTitleText">NOUVELLE VOITURE</span>
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fermer"
                ></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <div
                    id="formErrors"
                    class="alert alert-danger d-none"
                    role="alert"
                ></div>

                <form id="voitureForm">
                    @csrf

                    <input
                        type="hidden"
                        name="id"
                        id="rowId"
                    >

                    {{-- IDENTIFICATION --}}
                    <div class="form-section-title">
                        <i class="bi bi-card-text"></i>
                        Identification du véhicule
                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input
                                    type="text"
                                    class="form-control text-uppercase"
                                    id="immatriculation"
                                    name="immatriculation"
                                    placeholder="AA-123-AA"
                                    maxlength="255"
                                    required
                                >

                                <label for="immatriculation">
                                    Immatriculation
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="marque"
                                    name="marque"
                                    placeholder="Marque"
                                    maxlength="255"
                                    required
                                >

                                <label for="marque">
                                    Marque
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="modele"
                                    name="modele"
                                    placeholder="Modèle"
                                    maxlength="255"
                                    required
                                >

                                <label for="modele">
                                    Modèle
                                </label>
                            </div>
                        </div>

                    </div>

                    {{-- SITUATION DU VÉHICULE --}}
                    <div class="form-section-title">
                        <i class="bi bi-speedometer2"></i>
                        Situation du véhicule
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating flex-grow-1">
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="kilometrage"
                                        name="kilometrage"
                                        placeholder="Kilométrage"
                                        min="0"
                                        step="1"
                                        value="0"
                                    >

                                    <label for="kilometrage">
                                        Kilométrage
                                    </label>
                                </div>

                                <span class="input-group-text">
                                    km
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select
                                    class="form-select"
                                    id="statut"
                                    name="statut"
                                    required
                                >
                                    <option value="disponible">
                                        Disponible
                                    </option>

                                    <option value="réservé">
                                        Réservé
                                    </option>

                                    <option value="indisponible">
                                        Indisponible
                                    </option>
                                </select>

                                <label for="statut">
                                    Statut
                                </label>
                            </div>
                        </div>

                    </div>

                </form>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-light rounded-pill px-4"
                    data-bs-dismiss="modal"
                >
                    <i class="bi bi-x-circle"></i>
                    Annuler
                </button>

                <button
                    type="button"
                    id="saveButton"
                    class="btn btn-primary rounded-pill px-4"
                    onclick="saveData()"
                >
                    <i class="bi bi-save"></i>
                    Enregistrer
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
    let popup = null;

    const popupElement = document.getElementById('popupForm');
    const voitureForm = document.getElementById('voitureForm');
    const formErrors = document.getElementById('formErrors');
    const saveButton = document.getElementById('saveButton');

    function getPopup() {
        if (!popup) {
            popup = bootstrap.Modal.getOrCreateInstance(popupElement);
        }

        return popup;
    }

    function resetErrors() {
        formErrors.classList.add('d-none');
        formErrors.innerHTML = '';

        voitureForm
            .querySelectorAll('.is-invalid')
            .forEach(element => {
                element.classList.remove('is-invalid');
            });
    }

    function openNewVoiture() {
        const form = document.getElementById('voitureForm');

        form.reset();
        resetErrors();

        document.getElementById('rowId').value = '';
        document.getElementById('kilometrage').value = 0;
        document.getElementById('statut').value = 'disponible';

        document.getElementById('modalTitleText').textContent =
            'NOUVELLE VOITURE';

        getPopup().show();
    }

    function openEditVoiture(card) {
        const data = card.dataset;

        voitureForm.reset();
        resetErrors();

        document.getElementById('rowId').value =
            data.id ?? '';

        document.getElementById('immatriculation').value =
            data.immatriculation ?? '';

        document.getElementById('marque').value =
            data.marque ?? '';

        document.getElementById('modele').value =
            data.modele ?? '';

        document.getElementById('kilometrage').value =
            data.kilometrage ?? 0;

        document.getElementById('statut').value =
            data.statut ?? 'disponible';

        document.getElementById('modalTitleText').textContent =
            'MODIFICATION DE LA VOITURE';

        getPopup().show();
    }

    function displayValidationErrors(errors) {
        resetErrors();

        const errorList = document.createElement('ul');
        errorList.classList.add('mb-0');

        Object.entries(errors).forEach(([field, messages]) => {
            const input = document.getElementById(field);

            if (input) {
                input.classList.add('is-invalid');
            }

            messages.forEach(message => {
                const item = document.createElement('li');
                item.textContent = message;
                errorList.appendChild(item);
            });
        });

        formErrors.appendChild(errorList);
        formErrors.classList.remove('d-none');
    }

    async function saveData() {
        resetErrors();

        if (!voitureForm.checkValidity()) {
            voitureForm.reportValidity();
            return;
        }

        const formData = new FormData(voitureForm);

        saveButton.disabled = true;
        saveButton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm"
                aria-hidden="true"
            ></span>
            Enregistrement...
        `;

        try {
            const response = await fetch(
                "{{ route('voitures.store') }}",
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                '#voitureForm input[name="_token"]'
                            ).value,

                        'Accept': 'application/json',
                    },
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    displayValidationErrors(data.errors);
                    return;
                }

                throw new Error(
                    data.message ??
                    'Erreur lors de l’enregistrement.'
                );
            }

            getPopup().hide();
            window.location.reload();

        } catch (error) {
            console.error(error);

            formErrors.textContent =
                error.message ??
                'Une erreur est survenue lors de l’enregistrement.';

            formErrors.classList.remove('d-none');

        } finally {
            saveButton.disabled = false;

            saveButton.innerHTML = `
                <i class="bi bi-save"></i>
                Enregistrer
            `;
        }
    }

    document
        .getElementById('immatriculation')
        .addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });

    popupElement.addEventListener('hidden.bs.modal', function () {
        voitureForm.reset();
        resetErrors();

        document.getElementById('rowId').value = '';
        document.getElementById('statut').value = 'disponible';
    });
</script>

@endpush