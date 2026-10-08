<x-layout title="{{ __('Choose a new password') }} | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">{{ __('User access') }}</p>
                <h1>{{ __('Choose a new password') }}</h1>

                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autofocus>
                    <label for="password">{{ __('New password') }}</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    <label for="password_confirmation">{{ __('Confirm password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    <button type="submit">{{ __('Reset password') }}</button>
                </form>
            </div>
        </section>
    </main>
</x-layout>
