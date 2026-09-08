<x-layout title="Ielogoties | Abrugis">
    <main>
        <h1>Ielogoties</h1>

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Parole</label>
            <input id="password" name="password" type="password" required>

            <label>
                <input type="checkbox" name="remember" value="1">
                Atcerēties mani
            </label>

            <button type="submit">Ielogoties</button>
        </form>

        <p>Vēl nav konta? <a href="{{ route('register') }}">Reģistrēties</a></p>
    </main>
</x-layout>