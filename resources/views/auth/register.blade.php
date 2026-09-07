<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reģistrēties</title>
</head>
<body>
    <main>
        <h1>Reģistrēties</h1>

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="/register">
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

        <a href="{{ route('login') }}">Atpakaļ uz login</a>
    </main>
</body>
</html>
