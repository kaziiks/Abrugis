<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Abrugis' }}</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @vite('resources/js/app.js')
    <script src="{{ asset('js/compare.js') }}" defer></script>
    <script src="{{ asset('js/area-calculator.js') }}" defer></script>
</head>
<body class="{{ $bodyClass ?? '' }}">
    <header>
        <nav class="nav" aria-label="{{ __('Main navigation') }}">
            <a class="logo" href="{{ url('/') }}">ABRUGIS</a>
            <div class="nav-links">
                <a href="{{ url('/#projects') }}">{{ __('Works') }}</a>
                <a href="{{ url('/#about') }}">{{ __('About us') }}</a>
                <a href="{{ url('/#how-we-work') }}">{{ __('How we work') }}</a>
                <a href="{{ url('/#contact') }}">{{ __('Contacts') }}</a>
                @if (auth()->user()?->role === 'admin')
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-trigger" type="button" aria-haspopup="true">{{ __('Admin panel') }}</button>
                        <div class="nav-dropdown-menu">
                            <a href="{{ route('admin.dashboard') }}">{{ __('Consultation calendar') }}</a>
                            <a href="{{ route('admin.applications') }}">{{ __('Applications inbox') }}</a>
                            <a href="{{ route('admin.portfolio') }}">{{ __('Portfolio') }}</a>
                        </div>
                    </div>
                @else
                    <a href="{{ route('calc') }}">{{ __('Calculator') }}</a>
                    @auth
                        <div class="nav-dropdown">
                            <button class="nav-dropdown-trigger" type="button" aria-haspopup="true">{{ __('Applications') }}</button>
                            <div class="nav-dropdown-menu">
                                <a href="{{ route('form') }}">{{ __('Create an application') }}</a>
                                <a href="{{ route('applications') }}">{{ __('My applications') }}</a>
                                <a href="{{ route('calendar') }}">{{ __('Consultation calendar') }}</a>
                            </div>
                        </div>
                    @endauth
                @endif
            </div>
            <div class="auth-links">
                <div class="language-switcher" aria-label="{{ __('Choose language') }}">
                    <a href="{{ route('language', 'lv') }}" @class(['active' => app()->getLocale() === 'lv'])>LV</a>
                    <span>/</span>
                    <a href="{{ route('language', 'en') }}" @class(['active' => app()->getLocale() === 'en'])>EN</a>
                    <span>/</span>
                    <a href="{{ route('language', 'ru') }}" @class(['active' => app()->getLocale() === 'ru'])>RU</a>
                </div>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">{{ __('Log out') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">{{ __('Log in') }}</a>
                @endauth
            </div>
        </nav>
    </header>

    {{ $slot }}
</body>
</html>
