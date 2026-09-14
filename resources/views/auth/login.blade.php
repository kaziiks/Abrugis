<x-layout title="Ielogoties | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">Piekļuve lietotājam</p>
                <h1>Ielogoties</h1>

                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <label for="email">E-pasts</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

                    <label for="password">Parole</label>
                    <input id="password" name="password" type="password" required>

                    <label class="checkbox-row">
                        <input type="checkbox" name="remember" value="1">
                        <span>Atcerēties mani</span>
                    </label>

                    <button type="submit">Ielogoties</button>
                </form>

                <p class="auth-meta">Vēl nav konta? <a href="{{ route('register') }}">Reģistrēties</a></p>
            </div>
        </section>
    </main>
</x-layout>