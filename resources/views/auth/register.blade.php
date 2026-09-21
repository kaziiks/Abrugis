<x-layout title="{{ __('Register') }} | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">{{ __('Create an account') }}</p>
                <h1>{{ __('Register') }}</h1>

                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf
                    <label for="name">{{ __('Name') }}</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>

                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>

                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" required>

                    <label for="password_confirmation">{{ __('Confirm password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>

                    <button type="submit">{{ __('Register') }}</button>
                </form>

                <p class="auth-meta"><a href="{{ route('login') }}">{{ __('Back to log in') }}</a></p>
            </div>
        </section>
    </main>
</x-layout>
