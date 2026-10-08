<x-layout title="{{ __('Reset password') }} | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">{{ __('User access') }}</p>
                <h1>{{ __('Reset password') }}</h1>
                <p>{{ __('Enter your account email address and we will send a password reset link.') }}</p>

                @if (session('status'))
                    <p class="success-note" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                    @csrf
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                    <button type="submit">{{ __('Email password reset link') }}</button>
                </form>

                <p class="auth-meta"><a href="{{ route('login') }}">{{ __('Back to log in') }}</a></p>
            </div>
        </section>
    </main>
</x-layout>
