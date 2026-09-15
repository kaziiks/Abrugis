<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Abrugis' }}</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body class="{{ $bodyClass ?? '' }}">
    <header>
        <nav class="nav" aria-label="Galvenā navigācija">
            <a class="logo" href="{{ url('/') }}">ABRUGIS</a>
            <div class="nav-links">
                <a href="{{ url('/#darbi') }}">Darbi</a>
                <a href="{{ url('/#par-mums') }}">Par mums</a>
                <a href="{{ url('/#kontakti') }}">Kontakti</a>
                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Admin panelis</a>
                @else
                    <a href="{{ route('calc') }}">Kalkulators</a>
                    @auth
                        <a href="{{ route('form') }}">Izveidot pieteikumu</a>
                    @endauth
                @endif
            </div>
            <div class="auth-links">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Iziet</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Ienākt</a>
                @endauth
            </div>
        </nav>
    </header>

    {{ $slot }}
</body>
</html>
