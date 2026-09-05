<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons-1.13.1/bootstrap-icons.min.css') }}">
</head>

<body class="bg-light">

<div class="container py-4">
    @yield('content')
</div>

<script src="{{ asset('bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
