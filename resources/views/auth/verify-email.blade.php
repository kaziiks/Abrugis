<x-layout title="{{ __('Verify your email') }} | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">{{ __('User access') }}</p>
                <h1>{{ __('Verify your email') }}</h1>
                <p>{{ __('Before continuing, confirm your email address using the verification link we sent you.') }}</p>

                @if (session('status') === 'verification-link-sent')
                    <p class="success-note" role="status">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="auth-form">
                    @csrf
                    <button type="submit">{{ __('Resend verification email') }}</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="auth-form">
                    @csrf
                    <button type="submit">{{ __('Log out') }}</button>
                </form>
            </div>
        </section>
    </main>
</x-layout>
