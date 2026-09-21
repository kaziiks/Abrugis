<x-layout title="{{ __('Log in') }} | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">{{ __('User access') }}</p>
                <h1>{{ __('Log in') }}</h1>

                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" required>

                    <label class="checkbox-row">
                        <input type="checkbox" name="remember" value="1">
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    <button type="submit">{{ __('Log in') }}</button>
                </form>

                <p class="auth-meta">{{ __('No account yet?') }} <a href="{{ route('register') }}">{{ __('Register') }}</a></p>
            </div>
        </section>
    </main>
</x-layout>