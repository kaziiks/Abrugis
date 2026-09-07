<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ienākt</title>
</head>
<body>
	<main>
		<h1>Ienākt</h1>

		@if ($errors->any())
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		@endif

		<form method="POST" action="/login">
			@csrf
			<label for="email">E-pasts</label>
			<input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

			<label for="password">Parole</label>
			<input id="password" name="password" type="password" required>

			<label>
				<input type="checkbox" name="remember" value="1">
				Atcerēties mani
			</label>

			<button type="submit">Ienākt</button>
		</form>

		<a href="{{ route('register') }}">Reģistrēties</a>
	</main>
</body>
</html>