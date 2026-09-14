<x-layout title="Reģistrēties | Abrugis">
    <main>
        <section class="auth-shell">
            <div class="auth-card">
                <p class="eyebrow">Izveidot kontu</p>
                <h1>Reģistrēties</h1>

                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf
                    <label for="name">Vārds</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>

                    <label for="email">E-pasts</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>

                    <label for="password">Parole</label>
                    <input id="password" name="password" type="password" required>

                    <label for="password_confirmation">Atkārtot paroli</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>

                    <button type="submit">Reģistrēties</button>
                </form>

                <p class="auth-meta"><a href="{{ route('login') }}">Atpakaļ uz login</a></p>
            </div>
        </section>
    </main>
</x-layout>
