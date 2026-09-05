@php
    $user = auth()->user();
@endphp

<div id="sidebar-wrapper">

    <ul class="sidebar-nav">

        {{-- =====================================================
        ADMINISTRATEUR ET GESTIONNAIRE
        ====================================================== --}}
        @if($user->isAdmin() || $user->isGestion())

            {{-- Planning général --}}
            <li>
                <a
                    href="{{ route('planning.index') }}"
                    class="nav-link
                        {{ request()->routeIs('planning.*')
                            ? 'active'
                            : '' }}"
                >
                    <i class="bi bi-calendar3 sidebar-icon"></i>
                    <span>Planning général</span>
                </a>
            </li>

            {{-- Véhicules --}}
            <li>
                <a
                    href="{{ route('voitures.index') }}"
                    class="nav-link
                        {{ request()->routeIs('voitures.*')
                            ? 'active'
                            : '' }}"
                >
                    <i class="bi bi-car-front-fill sidebar-icon"></i>
                    <span>Véhicules</span>
                </a>
            </li>

        @endif

        {{-- =====================================================
        ADMINISTRATEUR UNIQUEMENT
        ====================================================== --}}
        @if($user->isAdmin())

            <li>
                <a
                    href="{{ route('utilisateurs.index') }}"
                    class="nav-link
                        {{ request()->routeIs('utilisateurs.*')
                            ? 'active'
                            : '' }}"
                >
                    <i class="bi bi-people sidebar-icon"></i>
                    <span>Utilisateurs</span>
                </a>
            </li>

        @endif

        {{-- =====================================================
        COLLABORATEUR UNIQUEMENT
        ====================================================== --}}
        @if($user->isCollaborateur())

            {{-- Dashboard personnel --}}
            <li>
                <a
                    href="{{ route('collaborateur.dashboard') }}"
                    class="nav-link
                        {{ request()->routeIs('collaborateur.dashboard')
                            ? 'active'
                            : '' }}"
                >
                    <i class="bi bi-speedometer2 sidebar-icon"></i>
                    <span>Mon dashboard</span>
                </a>
            </li>

        @endif

    </ul>

</div>