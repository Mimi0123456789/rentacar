```blade
@extends('layouts.app')

@section('content')

<style>
    .collaborateur-dashboard {
        --collab-primary: #dc3545;
        --collab-primary-dark: #b02a37;
        --collab-primary-soft: #fff1f2;
        --collab-success: #198754;
        --collab-success-soft: #d1e7dd;
        --collab-warning: #b45309;
        --collab-warning-soft: #fff3cd;
        --collab-info: #0d6efd;
        --collab-info-soft: #e7f1ff;
        --collab-text: #1f2937;
        --collab-muted: #6b7280;
        --collab-border: #e5e7eb;
        --collab-background: #f5f7fa;
        --collab-card: #ffffff;
        --collab-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);

        width: min(1450px, 95%);
        margin: 0 auto;
        padding: 30px 0 50px;
        color: var(--collab-text);
    }

    .collaborateur-dashboard * {
        box-sizing: border-box;
    }

    /*
    |--------------------------------------------------------------------------
    | En-tête
    |--------------------------------------------------------------------------
    */

    .collab-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;

        margin-bottom: 24px;
        padding: 28px;

        background:
            linear-gradient(
                135deg,
                rgba(220, 53, 69, 0.97),
                rgba(176, 42, 55, 0.97)
            );

        color: #ffffff;
        border-radius: 22px;
        box-shadow: var(--collab-shadow);
    }

    .collab-header-content {
        min-width: 0;
    }

    .collab-header-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        margin-bottom: 8px;

        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        opacity: 0.82;
    }

    .collab-header h1 {
        margin: 0;
        font-size: clamp(1.6rem, 3vw, 2.25rem);
        font-weight: 800;
    }

    .collab-header p {
        margin: 8px 0 0;
        font-size: 0.98rem;
        opacity: 0.9;
    }

    .collab-new-request-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        flex-shrink: 0;

        min-height: 48px;
        padding: 0 22px;

        border: 0;
        border-radius: 999px;

        background: #ffffff;
        color: var(--collab-primary-dark);

        font-weight: 800;
        text-decoration: none;

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;
    }

    .collab-new-request-button:hover {
        color: var(--collab-primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 9px 24px rgba(0, 0, 0, 0.16);
    }

    /*
    |--------------------------------------------------------------------------
    | Indicateurs
    |--------------------------------------------------------------------------
    */

    .collab-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .collab-kpi-card {
        display: flex;
        align-items: center;
        gap: 16px;

        min-width: 0;
        padding: 21px;

        background: var(--collab-card);
        border: 1px solid var(--collab-border);
        border-radius: 18px;
        box-shadow: var(--collab-shadow);
    }

    .collab-kpi-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        flex: 0 0 54px;
        width: 54px;
        height: 54px;

        border-radius: 16px;
        font-size: 1.45rem;
    }

    .collab-kpi-icon.pending {
        background: var(--collab-warning-soft);
        color: var(--collab-warning);
    }

    .collab-kpi-icon.current {
        background: var(--collab-success-soft);
        color: var(--collab-success);
    }

    .collab-kpi-icon.future {
        background: var(--collab-info-soft);
        color: var(--collab-info);
    }

    .collab-kpi-value {
        margin-bottom: 4px;
        font-size: 1.8rem;
        font-weight: 850;
        line-height: 1;
    }

    .collab-kpi-label {
        color: var(--collab-muted);
        font-size: 0.88rem;
        font-weight: 600;
    }

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    .collab-section {
        margin-bottom: 24px;
        padding: 24px;

        background: var(--collab-card);
        border: 1px solid var(--collab-border);
        border-radius: 20px;
        box-shadow: var(--collab-shadow);
    }

    .collab-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        margin-bottom: 20px;
        padding-bottom: 15px;

        border-bottom: 1px solid var(--collab-border);
    }

    .collab-section-title {
        display: flex;
        align-items: center;
        gap: 10px;

        min-width: 0;
    }

    .collab-section-title-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 40px;
        height: 40px;

        flex: 0 0 40px;

        border-radius: 12px;
    }

    .collab-section-title-icon.pending {
        color: var(--collab-warning);
        background: var(--collab-warning-soft);
    }

    .collab-section-title-icon.current {
        color: var(--collab-success);
        background: var(--collab-success-soft);
    }

    .collab-section-title-icon.future {
        color: var(--collab-info);
        background: var(--collab-info-soft);
    }

    .collab-section-title-icon.history {
        color: #4b5563;
        background: #f3f4f6;
    }

    .collab-section-header h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
    }

    .collab-section-description {
        margin: 3px 0 0;
        color: var(--collab-muted);
        font-size: 0.82rem;
    }

    .collab-section-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-width: 34px;
        height: 34px;
        padding: 0 10px;

        border-radius: 999px;

        background: #f3f4f6;
        color: #374151;

        font-size: 0.82rem;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Cartes de réservation
    |--------------------------------------------------------------------------
    */

    .collab-reservation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }

    .collab-reservation-card {
        position: relative;

        display: flex;
        flex-direction: column;
        gap: 16px;

        min-width: 0;
        padding: 20px;

        border: 1px solid var(--collab-border);
        border-radius: 16px;
        background: #ffffff;

        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .collab-reservation-card:hover {
        transform: translateY(-2px);
        border-color: #d1d5db;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    }

    .collab-reservation-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .collab-reservation-card-title {
        min-width: 0;
    }

    .collab-reservation-card-title h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .collab-reservation-card-title p {
        margin: 4px 0 0;
        color: var(--collab-muted);
        font-size: 0.8rem;
    }

    .collab-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        flex-shrink: 0;

        padding: 6px 10px;
        border-radius: 999px;

        font-size: 0.7rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.035em;
    }

    .collab-status.pending {
        color: var(--collab-warning);
        background: var(--collab-warning-soft);
    }

    .collab-status.current {
        color: var(--collab-success);
        background: var(--collab-success-soft);
    }

    .collab-status.future {
        color: var(--collab-info);
        background: var(--collab-info-soft);
    }

    .collab-status.finished {
        color: #4b5563;
        background: #f3f4f6;
    }

    .collab-details {
        display: grid;
        gap: 10px;
    }

    .collab-detail-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding-bottom: 9px;

        border-bottom: 1px dashed #eceff3;
        font-size: 0.87rem;
    }

    .collab-detail-row:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .collab-detail-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        color: var(--collab-muted);
        white-space: nowrap;
    }

    .collab-detail-value {
        color: var(--collab-text);
        font-weight: 750;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .collab-motif {
        padding: 13px 14px;

        background: #f8fafc;
        border: 1px solid #edf0f3;
        border-radius: 12px;

        font-size: 0.85rem;
        line-height: 1.5;
    }

    .collab-motif-label {
        display: block;

        margin-bottom: 5px;

        color: var(--collab-muted);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /*
    |--------------------------------------------------------------------------
    | Historique
    |--------------------------------------------------------------------------
    */

    .collab-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--collab-border);
        border-radius: 14px;
    }

    .collab-history-table {
        width: 100%;
        min-width: 900px;

        border-collapse: collapse;
        background: #ffffff;
    }

    .collab-history-table th,
    .collab-history-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--collab-border);
        text-align: left;
        vertical-align: middle;
    }

    .collab-history-table th {
        background: #f8fafc;
        color: var(--collab-muted);

        font-size: 0.72rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.045em;
        white-space: nowrap;
    }

    .collab-history-table td {
        font-size: 0.86rem;
    }

    .collab-history-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .collab-history-table tbody tr:hover {
        background: #fafbfc;
    }

    .collab-vehicle-name {
        display: block;
        font-weight: 800;
    }

    .collab-vehicle-registration {
        display: block;
        margin-top: 3px;
        color: var(--collab-muted);
        font-size: 0.78rem;
    }

    /*
    |--------------------------------------------------------------------------
    | États vides
    |--------------------------------------------------------------------------
    */

    .collab-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        min-height: 170px;
        padding: 26px;

        border: 1px dashed #d5d9df;
        border-radius: 15px;
        background: #fafbfc;

        color: var(--collab-muted);
        text-align: center;
    }

    .collab-empty-state i {
        margin-bottom: 10px;
        color: #9ca3af;
        font-size: 2rem;
    }

    .collab-empty-state strong {
        margin-bottom: 4px;
        color: #374151;
        font-size: 0.95rem;
    }

    .collab-empty-state span {
        font-size: 0.82rem;
    }

    /*
    |--------------------------------------------------------------------------
    | Modale
    |--------------------------------------------------------------------------
    */

    .collab-modal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 20px;
        box-shadow: 0 24px 65px rgba(15, 23, 42, 0.22);
    }

    .collab-modal .modal-header {
        padding: 20px 24px;

        background:
            linear-gradient(
                135deg,
                var(--collab-primary),
                var(--collab-primary-dark)
            );

        color: #ffffff;
        border-bottom: 0;
    }

    .collab-modal .modal-title {
        display: flex;
        align-items: center;
        gap: 10px;

        font-size: 1rem;
        font-weight: 850;
        letter-spacing: 0.02em;
    }

    .collab-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.85;
    }

    .collab-modal .modal-body {
        padding: 24px;
    }

    .collab-modal .modal-footer {
        padding: 17px 24px;
        background: #f8fafc;
        border-top: 1px solid var(--collab-border);
    }

    .collab-form-section {
        margin-bottom: 22px;
    }

    .collab-form-section:last-child {
        margin-bottom: 0;
    }

    .collab-form-title {
        display: flex;
        align-items: center;
        gap: 8px;

        margin-bottom: 13px;
        padding-bottom: 8px;

        border-bottom: 1px solid var(--collab-border);

        color: var(--collab-primary-dark);
        font-size: 0.76rem;
        font-weight: 850;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .collab-form-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        margin-top: 20px;
        padding: 13px 14px;

        border-radius: 12px;

        background: var(--collab-info-soft);
        color: #084298;

        font-size: 0.83rem;
        line-height: 1.45;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 992px) {
        .collab-kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .collaborateur-dashboard {
            width: min(100% - 24px, 1450px);
            padding-top: 18px;
        }

        .collab-header {
            align-items: stretch;
            flex-direction: column;
            padding: 22px;
            border-radius: 18px;
        }

        .collab-new-request-button {
            width: 100%;
        }

        .collab-section {
            padding: 18px;
            border-radius: 17px;
        }

        .collab-section-header {
            align-items: flex-start;
        }

        .collab-reservation-grid {
            grid-template-columns: 1fr;
        }

        .collab-modal .modal-body {
            padding: 18px;
        }
    }

    @media (max-width: 480px) {
        .collab-kpi-card {
            padding: 17px;
        }

        .collab-reservation-card-header {
            flex-direction: column;
        }

        .collab-status {
            align-self: flex-start;
        }

        .collab-detail-row {
            flex-direction: column;
            gap: 4px;
        }

        .collab-detail-value {
            text-align: left;
        }
    }
</style>

<div class="collaborateur-dashboard">

    {{-- ============================================================
    | EN-TÊTE
    ============================================================ --}}

    <header class="collab-header">

        <div class="collab-header-content">

            <div class="collab-header-eyebrow">
                <i class="bi bi-person-circle"></i>
                Espace collaborateur
            </div>

            <h1>
                Bonjour {{ $user->name ?: $user->login }}
            </h1>

            <p>
                Consultez vos demandes et suivez vos réservations de véhicules.
            </p>

        </div>

        <button
            type="button"
            class="collab-new-request-button"
            onclick="openDemandeModal()"
        >
            <i class="bi bi-plus-circle-fill"></i>
            Nouvelle demande
        </button>

    </header>

    {{-- ============================================================
    | INDICATEURS
    ============================================================ --}}

    <section class="collab-kpi-grid">

        <article class="collab-kpi-card">

            <div class="collab-kpi-icon pending">
                <i class="bi bi-hourglass-split"></i>
            </div>

            <div>
                <div class="collab-kpi-value">
                    {{ $demandesEnCours->count() }}
                </div>

                <div class="collab-kpi-label">
                    Demande(s) en attente
                </div>
            </div>

        </article>

        <article class="collab-kpi-card">

            <div class="collab-kpi-icon current">
                <i class="bi bi-car-front-fill"></i>
            </div>

            <div>
                <div class="collab-kpi-value">
                    {{ $reservationsActuelles->count() }}
                </div>

                <div class="collab-kpi-label">
                    Réservation(s) actuelle(s)
                </div>
            </div>

        </article>

        <article class="collab-kpi-card">

            <div class="collab-kpi-icon future">
                <i class="bi bi-calendar-event"></i>
            </div>

            <div>
                <div class="collab-kpi-value">
                    {{ $reservationsAVenir->count() }}
                </div>

                <div class="collab-kpi-label">
                    Réservation(s) à venir
                </div>
            </div>

        </article>

    </section>

    {{-- ============================================================
    | DEMANDES EN COURS
    ============================================================ --}}

    <section class="collab-section">

        <div class="collab-section-header">

            <div class="collab-section-title">

                <div class="collab-section-title-icon pending">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <div>
                    <h2>Mes demandes en cours</h2>

                    <p class="collab-section-description">
                        Demandes transmises et en attente de traitement.
                    </p>
                </div>

            </div>

            <span class="collab-section-count">
                {{ $demandesEnCours->count() }}
            </span>

        </div>

        @forelse($demandesEnCours as $demande)

            @if($loop->first)
                <div class="collab-reservation-grid">
            @endif

            <article
                class="collab-reservation-card"
                id="demande-{{ $demande->id }}"
            >

                <div class="collab-reservation-card-header">

                    <div class="collab-reservation-card-title">

                        <h3>
                            Demande n°{{ $demande->id }}
                        </h3>

                        <p>
                            Créée le
                            {{ $demande->created_at?->format('d/m/Y à H:i') ?? 'Non renseigné' }}
                        </p>

                    </div>

                    <span class="collab-status pending">
                        <i class="bi bi-clock"></i>
                        En attente
                    </span>

                </div>

                <div class="collab-details">

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-calendar-plus"></i>
                            Départ
                        </span>

                        <span class="collab-detail-value">
                            {{ $demande->date_debut?->format('d/m/Y à H:i') ?? 'Non renseigné' }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-calendar-minus"></i>
                            Retour
                        </span>

                        <span class="collab-detail-value">
                            {{ $demande->date_fin?->format('d/m/Y à H:i') ?? 'Non renseigné' }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-people"></i>
                            Passagers
                        </span>

                        <span class="collab-detail-value">
                            {{ $demande->nb_passagers ?? 1 }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-luggage"></i>
                            Bagages
                        </span>

                        <span class="collab-detail-value">
                            {{ $demande->bagages ? 'Oui' : 'Non' }}
                        </span>

                    </div>

                </div>

                <div class="collab-motif">

                    <span class="collab-motif-label">
                        Motif du déplacement
                    </span>

                    <p>
                        {{ $demande->motif }}
                    </p>

                </div>

                <div class="collab-reservation-actions">

                    <div class="collab-card-actions">

                        <button
                            type="button"
                            class="collab-btn collab-btn-edit"
                            id="edit-demande-button-{{ $demande->id }}"
                            data-url="{{ route('collaborateur.demandes.edit', ['reservation' => $demande->id]) }}"
                            data-reservation-id="{{ $demande->id }}"
                            onclick="modifierDemande(this)"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button
                            type="button"
                            class="collab-btn collab-btn-cancel"
                            id="cancel-demande-button-{{ $demande->id }}"
                            data-url="{{ route('collaborateur.demandes.destroy', ['reservation' => $demande->id]) }}"
                            data-reservation-id="{{ $demande->id }}"
                            onclick="annulerDemande(this)"
                        >
                            <i class="bi bi-trash3"></i>
                            
                        </button>

                    </div>

                </div>

            </article>

            @if($loop->last)
                </div>
            @endif

        @empty

            <div class="collab-empty-state" id="empty-demandes-state">

                <i class="bi bi-inbox"></i>

                <strong>Aucune demande en attente</strong>

                <span>
                    Utilisez le bouton « Nouvelle demande » pour effectuer
                    une réservation.
                </span>

            </div>

        @endforelse

    </section>

    {{-- ============================================================
    | RÉSERVATIONS ACTUELLES
    ============================================================ --}}

    <section class="collab-section">

        <div class="collab-section-header">

            <div class="collab-section-title">

                <div class="collab-section-title-icon current">
                    <i class="bi bi-car-front-fill"></i>
                </div>

                <div>
                    <h2>Mes réservations actuelles</h2>

                    <p class="collab-section-description">
                        Véhicules actuellement réservés à votre nom.
                    </p>
                </div>

            </div>

            <span class="collab-section-count">
                {{ $reservationsActuelles->count() }}
            </span>

        </div>

        @forelse($reservationsActuelles as $reservation)

            @if($loop->first)
                <div class="collab-reservation-grid">
            @endif

            <article class="collab-reservation-card">

                <div class="collab-reservation-card-header">

                    <div class="collab-reservation-card-title">

                        <h3>
                            @if($reservation->voiture)
                                {{ $reservation->voiture->marque }}
                                {{ $reservation->voiture->modele }}
                            @else
                                Véhicule non attribué
                            @endif
                        </h3>

                        <p>
                            {{ $reservation->voiture?->immatriculation ?? 'Immatriculation indisponible' }}
                        </p>

                    </div>

                    <span class="collab-status current">
                        <i class="bi bi-check-circle"></i>
                        En cours
                    </span>

                </div>

                <div class="collab-details">

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-calendar-plus"></i>
                            Départ
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->date_debut->format('d/m/Y à H:i') }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-calendar-minus"></i>
                            Retour prévu
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->date_fin->format('d/m/Y à H:i') }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-people"></i>
                            Passagers
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->nb_passagers }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-luggage"></i>
                            Bagages
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->bagages ? 'Oui' : 'Non' }}
                        </span>

                    </div>

                </div>

                <div class="collab-motif">

                    <span class="collab-motif-label">
                        Motif
                    </span>

                    {{ $reservation->motif }}

                </div>

            </article>

            @if($loop->last)
                </div>
            @endif

        @empty

            <div class="collab-empty-state">
                <i class="bi bi-car-front"></i>

                <strong>Aucune réservation actuelle</strong>

                <span>
                    Vous n’avez aucun véhicule réservé pour le moment.
                </span>
            </div>

        @endforelse

    </section>

    {{-- ============================================================
    | RÉSERVATIONS À VENIR
    ============================================================ --}}

    <section class="collab-section">

        <div class="collab-section-header">

            <div class="collab-section-title">

                <div class="collab-section-title-icon future">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div>
                    <h2>Mes réservations à venir</h2>

                    <p class="collab-section-description">
                        Réservations validées pour une période future.
                    </p>
                </div>

            </div>

            <span class="collab-section-count">
                {{ $reservationsAVenir->count() }}
            </span>

        </div>

        @forelse($reservationsAVenir as $reservation)

            @if($loop->first)
                <div class="collab-reservation-grid">
            @endif

            <article class="collab-reservation-card">

                <div class="collab-reservation-card-header">

                    <div class="collab-reservation-card-title">

                        <h3>
                            @if($reservation->voiture)
                                {{ $reservation->voiture->marque }}
                                {{ $reservation->voiture->modele }}
                            @else
                                Véhicule en cours d’attribution
                            @endif
                        </h3>

                        <p>
                            {{ $reservation->voiture?->immatriculation ?? 'Immatriculation à venir' }}
                        </p>

                    </div>

                    <span class="collab-status future">
                        <i class="bi bi-calendar-check"></i>
                        À venir
                    </span>

                </div>

                <div class="collab-details">

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-calendar-plus"></i>
                            Départ
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->date_debut->format('d/m/Y à H:i') }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-calendar-minus"></i>
                            Retour
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->date_fin->format('d/m/Y à H:i') }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-people"></i>
                            Passagers
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->nb_passagers }}
                        </span>

                    </div>

                    <div class="collab-detail-row">

                        <span class="collab-detail-label">
                            <i class="bi bi-luggage"></i>
                            Bagages
                        </span>

                        <span class="collab-detail-value">
                            {{ $reservation->bagages ? 'Oui' : 'Non' }}
                        </span>

                    </div>

                </div>

                <div class="collab-motif">

                    <span class="collab-motif-label">
                        Motif
                    </span>

                    {{ $reservation->motif }}

                </div>

            </article>

            @if($loop->last)
                </div>
            @endif

        @empty

            <div class="collab-empty-state">
                <i class="bi bi-calendar-x"></i>

                <strong>Aucune réservation à venir</strong>

                <span>
                    Aucune réservation future n’est actuellement planifiée.
                </span>
            </div>

        @endforelse

    </section>

    {{-- ============================================================
    | HISTORIQUE
    ============================================================ --}}

    <section class="collab-section">

        <div class="collab-section-header">

            <div class="collab-section-title">

                <div class="collab-section-title-icon history">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div>
                    <h2>Historique de mes réservations</h2>

                    <p class="collab-section-description">
                        Les 10 dernières réservations terminées au maximum.
                    </p>
                </div>

            </div>

            <span class="collab-section-count">
                {{ $historique->count() }}
            </span>

        </div>

        @if($historique->isNotEmpty())

            <div class="collab-table-wrapper">

                <table class="collab-history-table">

                    <thead>
                        <tr>
                            <th>Véhicule</th>
                            <th>Départ</th>
                            <th>Retour</th>
                            <th>Motif</th>
                            <th>Passagers</th>
                            <th>Kilométrage</th>
                            <th>Statut</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($historique as $reservation)

                            <tr>

                                <td>

                                    <span class="collab-vehicle-name">
                                        @if($reservation->voiture)
                                            {{ $reservation->voiture->marque }}
                                            {{ $reservation->voiture->modele }}
                                        @else
                                            Véhicule supprimé
                                        @endif
                                    </span>

                                    <span class="collab-vehicle-registration">
                                        {{ $reservation->voiture?->immatriculation ?? '—' }}
                                    </span>

                                </td>

                                <td>
                                    {{ $reservation->date_debut->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ $reservation->date_fin->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($reservation->motif, 55) }}
                                </td>

                                <td>
                                    {{ $reservation->nb_passagers }}
                                </td>

                                <td>
                                    @if(
                                        $reservation->kilometrage_depart !== null &&
                                        $reservation->kilometrage_retour !== null
                                    )
                                        {{ number_format(
                                            $reservation->kilometrage_retour
                                            - $reservation->kilometrage_depart,
                                            0,
                                            ',',
                                            ' '
                                        ) }}
                                        km
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    <span class="collab-status finished">
                                        <i class="bi bi-check2-circle"></i>
                                        Terminée
                                    </span>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="collab-empty-state">
                <i class="bi bi-clock-history"></i>

                <strong>Aucun historique</strong>

                <span>
                    Aucune réservation terminée n’est encore enregistrée.
                </span>
            </div>

        @endif

    </section>

</div>

{{-- ================================================================
| MODALE NOUVELLE DEMANDE
================================================================ --}}

<div
    class="modal fade collab-modal"
    id="demandeModal"
    tabindex="-1"
    aria-labelledby="demandeModalTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="demandeModalTitle"
                >
                    <i class="bi bi-calendar-plus-fill"></i>
                    Nouvelle demande de réservation
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fermer"
                ></button>

            </div>

            <div class="modal-body">

                <div
                    id="demandeErrors"
                    class="alert alert-danger d-none"
                    role="alert"
                ></div>

                <form id="demandeForm">
                    @csrf

                    <section class="collab-form-section">

                        <div class="collab-form-title">
                            <i class="bi bi-calendar-range"></i>
                            Période souhaitée
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input
                                        type="datetime-local"
                                        class="form-control"
                                        id="date_debut"
                                        name="date_debut"
                                        required
                                    >

                                    <label for="date_debut">
                                        Date et heure de départ
                                    </label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input
                                        type="datetime-local"
                                        class="form-control"
                                        id="date_fin"
                                        name="date_fin"
                                        required
                                    >

                                    <label for="date_fin">
                                        Date et heure de retour
                                    </label>

                                </div>

                            </div>

                        </div>

                    </section>

                    <section class="collab-form-section">

                        <div class="collab-form-title">
                            <i class="bi bi-info-circle"></i>
                            Informations du déplacement
                        </div>

                        <div class="row g-3">

                            <div class="col-md-8">

                                <div class="form-floating">

                                    <textarea
                                        class="form-control"
                                        id="motif"
                                        name="motif"
                                        placeholder="Motif"
                                        style="height: 125px"
                                        maxlength="1000"
                                        required
                                    ></textarea>

                                    <label for="motif">
                                        Motif du déplacement
                                    </label>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-floating">

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="nb_passagers"
                                        name="nb_passagers"
                                        min="1"
                                        max="20"
                                        value="1"
                                        required
                                    >

                                    <label for="nb_passagers">
                                        Nombre de passagers
                                    </label>

                                </div>

                            </div>

                            <div class="col-12">

                                <div class="form-check form-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="bagages"
                                        name="bagages"
                                        value="1"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="bagages"
                                    >
                                        Transport de bagages ou de matériel
                                    </label>

                                </div>

                            </div>

                        </div>

                        <div class="collab-form-note">

                            <i class="bi bi-info-circle-fill"></i>

                            <span>
                                La demande sera examinée par un gestionnaire.
                                Le véhicule sera attribué selon les disponibilités
                                et les besoins indiqués.
                            </span>

                        </div>

                    </section>

                </form>

            </div>

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
                    id="demandeSaveButton"
                    class="btn btn-danger rounded-pill px-4"
                    onclick="saveDemande()"
                >
                    <i class="bi bi-send-fill"></i>
                    Envoyer la demande
                </button>

            </div>

        </div>

    </div>
</div>

@endsection

@push('scripts')

<script>
    /*
    |--------------------------------------------------------------------------
    | État de la modale
    |--------------------------------------------------------------------------
    */

    let demandeMode = 'create';
    let demandeUpdateUrl = null;

    document.addEventListener('DOMContentLoaded', function () {
        initializeDemandeDates();
        initializeDemandeModalEvents();
    });

    /*
    |--------------------------------------------------------------------------
    | Initialisation
    |--------------------------------------------------------------------------
    */

    function initializeDemandeDates() {
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');
        const minimumDate = getCurrentLocalDateTime();

        if (dateDebut) {
            dateDebut.min = minimumDate;

            dateDebut.addEventListener('change', function () {
                updateDateFinMinimum();
            });
        }

        if (dateFin) {
            dateFin.min = minimumDate;
        }
    }

    function initializeDemandeModalEvents() {
        const modalElement = getDemandeModalElement();

        if (!modalElement) {
            return;
        }

        modalElement.addEventListener(
            'hidden.bs.modal',
            function () {
                resetDemandeModal();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    function getCurrentLocalDateTime() {
        const now = new Date();

        now.setMinutes(
            now.getMinutes() - now.getTimezoneOffset()
        );

        return now.toISOString().slice(0, 16);
    }

    function getDemandeModalElement() {
        return document.getElementById('demandeModal');
    }

    function getDemandeForm() {
        return document.getElementById('demandeForm');
    }

    function getDemandeErrors() {
        return document.getElementById('demandeErrors');
    }

    function getDemandeSaveButton() {
        return document.getElementById('demandeSaveButton');
    }

    function getDemandeModalTitle() {
        return document.getElementById('demandeModalTitle');
    }

    function getCsrfToken() {
        return (
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content')
            ??
            document
                .querySelector('input[name="_token"]')
                ?.value
            ??
            null
        );
    }

    async function parseJsonResponse(response) {
        const contentType =
            response.headers.get('content-type') || '';

        if (!contentType.includes('application/json')) {
            const responseText = await response.text();

            console.error(
                'Réponse serveur non JSON :',
                responseText
            );

            throw new Error(
                `Le serveur a retourné une erreur HTTP ${response.status}.`
            );
        }

        return await response.json();
    }

    function updateDateFinMinimum() {
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');

        if (!dateDebut || !dateFin) {
            return;
        }

        dateFin.min =
            dateDebut.value || getCurrentLocalDateTime();

        if (
            dateFin.value &&
            dateDebut.value &&
            dateFin.value <= dateDebut.value
        ) {
            dateFin.value = '';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Gestion des erreurs
    |--------------------------------------------------------------------------
    */

    function resetDemandeErrors() {
        const form = getDemandeForm();
        const errorsContainer = getDemandeErrors();

        if (errorsContainer) {
            errorsContainer.classList.add('d-none');
            errorsContainer.innerHTML = '';
        }

        if (form) {
            form
                .querySelectorAll('.is-invalid')
                .forEach(function (element) {
                    element.classList.remove('is-invalid');
                });
        }
    }

    function displayDemandeErrors(errors) {
        const errorsContainer = getDemandeErrors();

        if (!errorsContainer) {
            return;
        }

        resetDemandeErrors();

        const list = document.createElement('ul');
        list.classList.add('mb-0');

        Object.entries(errors).forEach(
            function ([field, messages]) {
                const input = document.getElementById(field);

                if (input) {
                    input.classList.add('is-invalid');
                }

                messages.forEach(function (message) {
                    const item = document.createElement('li');

                    item.textContent = message;
                    list.appendChild(item);
                });
            }
        );

        errorsContainer.appendChild(list);
        errorsContainer.classList.remove('d-none');
    }

    function displayGeneralDemandeError(message) {
        const errorsContainer = getDemandeErrors();

        if (!errorsContainer) {
            alert(message);
            return;
        }

        resetDemandeErrors();

        errorsContainer.textContent = message;
        errorsContainer.classList.remove('d-none');
    }

    /*
    |--------------------------------------------------------------------------
    | Réinitialisation de la modale
    |--------------------------------------------------------------------------
    */

    function resetDemandeModal() {
        const form = getDemandeForm();
        const modalTitle = getDemandeModalTitle();
        const saveButton = getDemandeSaveButton();

        demandeMode = 'create';
        demandeUpdateUrl = null;

        if (form) {
            form.reset();
        }

        resetDemandeErrors();

        const nbPassagers =
            document.getElementById('nb_passagers');

        const bagages =
            document.getElementById('bagages');

        const dateDebut =
            document.getElementById('date_debut');

        const dateFin =
            document.getElementById('date_fin');

        if (nbPassagers) {
            nbPassagers.value = 1;
        }

        if (bagages) {
            bagages.checked = false;
        }

        const minimumDate = getCurrentLocalDateTime();

        if (dateDebut) {
            dateDebut.min = minimumDate;
        }

        if (dateFin) {
            dateFin.min = minimumDate;
        }

        if (modalTitle) {
            modalTitle.innerHTML = `
                <i class="bi bi-calendar-plus-fill"></i>
                Nouvelle demande de réservation
            `;
        }

        if (saveButton) {
            saveButton.disabled = false;

            saveButton.innerHTML = `
                <i class="bi bi-send-fill"></i>
                Envoyer la demande
            `;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Ouverture en mode création
    |--------------------------------------------------------------------------
    */

    function openDemandeModal() {
        const modalElement = getDemandeModalElement();
        const form = getDemandeForm();

        if (!modalElement || !form) {
            console.error(
                'La modale ou le formulaire de demande est introuvable.'
            );

            return;
        }

        resetDemandeModal();

        bootstrap.Modal
            .getOrCreateInstance(modalElement)
            .show();
    }

    /*
    |--------------------------------------------------------------------------
    | Ouverture en mode modification
    |--------------------------------------------------------------------------
    */

    async function modifierDemande(button) {
        const reservationId = button.dataset.reservationId;
        const url = button.dataset.url;

        if (!reservationId || !url) {
            alert(
                'Impossible d’identifier la demande à modifier.'
            );

            return;
        }

        const originalButtonContent = button.innerHTML;

        button.disabled = true;

        button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm"
                aria-hidden="true"
            ></span>
            Chargement...
        `;

        try {
            const response = await fetch(url, {
                method: 'GET',

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },

                credentials: 'same-origin',
            });

            const data = await parseJsonResponse(response);

            if (!response.ok) {
                throw new Error(
                    data.message ||
                    'Impossible de charger la demande.'
                );
            }

            if (!data.reservation) {
                throw new Error(
                    'Les données de la demande sont absentes.'
                );
            }

            demandeMode = 'edit';
            demandeUpdateUrl = data.update_url;

            fillDemandeForm(data.reservation);

            const modalTitle = getDemandeModalTitle();
            const saveButton = getDemandeSaveButton();
            const modalElement = getDemandeModalElement();

            if (modalTitle) {
                modalTitle.innerHTML = `
                    <i class="bi bi-pencil-square"></i>
                    Modifier la demande n°${reservationId}
                `;
            }

            if (saveButton) {
                saveButton.innerHTML = `
                    <i class="bi bi-check-circle-fill"></i>
                    Enregistrer les modifications
                `;
            }

            if (!modalElement) {
                throw new Error(
                    'La modale de modification est introuvable.'
                );
            }

            resetDemandeErrors();

            bootstrap.Modal
                .getOrCreateInstance(modalElement)
                .show();

        } catch (error) {
            console.error(
                'Erreur lors du chargement de la demande :',
                error
            );

            alert(
                error.message ||
                'Une erreur est survenue lors du chargement.'
            );

        } finally {
            button.disabled = false;
            button.innerHTML = originalButtonContent;
        }
    }

    function fillDemandeForm(reservation) {
        const dateDebut =
            document.getElementById('date_debut');

        const dateFin =
            document.getElementById('date_fin');

        const motif =
            document.getElementById('motif');

        const nbPassagers =
            document.getElementById('nb_passagers');

        const bagages =
            document.getElementById('bagages');

        if (dateDebut) {
            dateDebut.value = reservation.date_debut || '';
            dateDebut.min = getCurrentLocalDateTime();
        }

        if (dateFin) {
            dateFin.value = reservation.date_fin || '';
            dateFin.min =
                reservation.date_debut ||
                getCurrentLocalDateTime();
        }

        if (motif) {
            motif.value = reservation.motif || '';
        }

        if (nbPassagers) {
            nbPassagers.value =
                reservation.nb_passagers ?? 1;
        }

        if (bagages) {
            bagages.checked =
                Boolean(reservation.bagages);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Création ou modification
    |--------------------------------------------------------------------------
    */

    async function saveDemande() {
        const form = getDemandeForm();
        const saveButton = getDemandeSaveButton();
        const modalElement = getDemandeModalElement();

        if (!form || !saveButton || !modalElement) {
            console.error(
                'Impossible de récupérer les éléments de la demande.'
            );

            return;
        }

        resetDemandeErrors();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        const bagages =
            document.getElementById('bagages');

        formData.set(
            'bagages',
            bagages && bagages.checked ? '1' : '0'
        );

        let url =
            "{{ route('collaborateur.demandes.store') }}";

        let method = 'POST';

        if (demandeMode === 'edit') {
            if (!demandeUpdateUrl) {
                displayGeneralDemandeError(
                    'L’URL de modification est introuvable.'
                );

                return;
            }

            url = demandeUpdateUrl;

            /*
             * Laravel traite la requête comme un PUT.
             * FormData reste envoyé en POST pour une meilleure compatibilité.
             */
            formData.set('_method', 'PUT');
        }

        const originalButtonContent = saveButton.innerHTML;

        saveButton.disabled = true;

        saveButton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm"
                aria-hidden="true"
            ></span>
            ${
                demandeMode === 'edit'
                    ? 'Modification...'
                    : 'Envoi...'
            }
        `;

        try {
            const response = await fetch(url, {
                method: method,

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },

                credentials: 'same-origin',
                body: formData,
            });

            const data = await parseJsonResponse(response);

            if (!response.ok) {
                if (
                    response.status === 422 &&
                    data.errors
                ) {
                    displayDemandeErrors(data.errors);
                    return;
                }

                throw new Error(
                    data.message ||
                    (
                        demandeMode === 'edit'
                            ? 'Impossible de modifier la demande.'
                            : 'Impossible d’enregistrer la demande.'
                    )
                );
            }

            bootstrap.Modal
                .getInstance(modalElement)
                ?.hide();

            alert(
                data.message ||
                (
                    demandeMode === 'edit'
                        ? 'Votre demande a été modifiée.'
                        : 'Votre demande a été enregistrée.'
                )
            );

            window.location.reload();

        } catch (error) {
            console.error(
                'Erreur lors de l’enregistrement :',
                error
            );

            displayGeneralDemandeError(
                error.message ||
                'Une erreur est survenue lors de l’enregistrement.'
            );

        } finally {
            saveButton.disabled = false;
            saveButton.innerHTML = originalButtonContent;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Annulation
    |--------------------------------------------------------------------------
    */

    async function annulerDemande(button) {
        const reservationId = button.dataset.reservationId;
        const url = button.dataset.url;

        if (!reservationId || !url) {
            console.error(
                'Identifiant ou URL d’annulation manquant.'
            );

            alert(
                'Impossible d’identifier la demande à annuler.'
            );

            return;
        }

        const confirmation = window.confirm(
            'Voulez-vous réellement annuler cette demande ?'
        );

        if (!confirmation) {
            return;
        }

        const csrfToken = getCsrfToken();

        if (!csrfToken) {
            console.error('Token CSRF introuvable.');

            alert(
                'Le token de sécurité est introuvable. Rechargez la page.'
            );

            return;
        }

        const originalButtonContent = button.innerHTML;

        button.disabled = true;

        button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm"
                aria-hidden="true"
            ></span>
            Annulation...
        `;

        try {
            const response = await fetch(url, {
                method: 'DELETE',

                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },

                credentials: 'same-origin',
            });

            const data = await parseJsonResponse(response);

            if (!response.ok) {
                throw new Error(
                    data.message ||
                    `Impossible d’annuler la demande (HTTP ${response.status}).`
                );
            }

            const demandeElement =
                document.getElementById(
                    `demande-${reservationId}`
                );

            if (demandeElement) {
                demandeElement.remove();
            }

            alert(
                data.message ||
                'Votre demande a bien été annulée.'
            );

            window.location.reload();

        } catch (error) {
            console.error(
                'Erreur lors de l’annulation :',
                error
            );

            alert(
                error.message ||
                'Une erreur est survenue pendant l’annulation.'
            );

            button.disabled = false;
            button.innerHTML = originalButtonContent;
        }
    }
</script>

@endpush
```