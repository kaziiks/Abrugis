<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Abrugis' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $bodyClass ?? '' }}">
    <header>
        <nav class="nav" aria-label="Galvenā navigācija">
            <a class="logo" href="{{ url('/') }}">ABRUGIS</a>
            <div class="nav-links">
                <a href="{{ url('/#darbi') }}">Darbi</a>
                <a href="{{ url('/#par-mums') }}">Par mums</a>
                <a href="{{ route('calc') }}">Kalkulators</a>
                <a href="{{ url('/#kontakti') }}">Kontakti</a>
                <a href="{{ route('form') }}">Izveidot pieteikumu</a>
            </div>
        </nav>
    </header>

    {{ $slot }}
</body>
</html>
