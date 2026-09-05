<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>RENT A CAR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons-1.13.1/bootstrap-icons.min.css') }}">

    <script src="{{ asset('bootstrap.bundle.min.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">



</head>

<body>

{{-- HEADER --}}
<header class="app-header">
    <div class="header-left">
        <span class="app-title">RENT A CAR</span>
    </div>

    <div class="header-center">
    </div>

    <div class="header-right">
        @if(auth()->check())
            <a href="{{ route('profile.edit') }}" class="me-3 text-white text-decoration-none fw-semibold" title="Modifier mon profil et mon mot de passe">
                {{ auth()->user()->name }}
            </a>
        @endif

        @if(auth()->check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light">Déconnexion</button>
            </form>
        @endif
    </div>
</header>

{{-- LAYOUT PRINCIPAL --}}
<div id="wrapper">
    @include('partials.sidebar')

    <div id="page-content-wrapper">
        @yield('content')
    </div>
</div>
@stack('scripts')
</body>
</html>
