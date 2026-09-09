<x-layout title="Bruģa kalkulators | Abrugis">
	<main class="calculator-page">
		<section class="calculator-intro">
			<h1>Uzzini sava<br><em>projekta cenu.</em></h1>
			<p class="calculator-lead">Ievadi platību, izvēlies bruģa veidu un pamatnes risinājumu. Gala cena var mainīties atkarībā no objekta un sagatavošanas darbiem.</p>
		</section>

		<section class="calculator-grid" aria-label="Bruģa cenas kalkulators">
			<form class="calculator-form" id="calculator-form" method="POST" action="{{ route('calc.calculate') }}">
				@csrf
				<div class="form-heading">
					<h2>Projekta parametri</h2>
				</div>

				<label for="area">Platība, m²</label>
				<div class="input-with-unit">
					<input id="area" name="area" type="number" min="1" max="100000" step="0.1" required>
					<span>m²</span>
				</div>

				<label for="paving">Bruģa veids</label>
				<select id="paving" name="paving">
					@forelse ($brugaVeidi as $brugaVeids)
						<option value="{{ $brugaVeids->price_per_m2 }}" data-name="{{ $brugaVeids->name }}" @selected(old('paving') == $brugaVeids->price_per_m2)>{{ $brugaVeids->name }} · {{ number_format($brugaVeids->price_per_m2, 2, ',', ' ') }} €/m²</option>
					@empty
						<option value="25" data-name="Betona bruģis" @selected(old('paving') == 25)>Betona bruģis · 25,00 €/m²</option>
						<option value="45" data-name="Granīta bruģakmens" @selected(old('paving') == 45)>Granīta bruģakmens · 45,00 €/m²</option>
					@endforelse
				</select>

				<fieldset>
					<legend>Pamatnes sagatavošana</legend>
					<label class="choice">
						<input type="radio" name="base" value="18" @checked(old('base', 18) == 18)>
						<span><strong>Standarta</strong><small>Smilts, šķembas un blietēšana</small></span>
						<b>18 €/m²</b>
					</label>
					<label class="choice">
						<input type="radio" name="base" value="28" @checked(old('base') == 28)>
						<span><strong>Pastiprināta</strong><small>Dziļāka pamatne lielākai slodzei</small></span>
						<b>28 €/m²</b>
					</label>
				</fieldset>

				<label class="checkbox-choice">
					<input id="removal" name="removal" type="checkbox" value="8" @checked(old('removal'))>
					<span><strong>Vecā seguma demontāža</strong><small>+8 €/m²</small></span>
				</label>

				<button class="calculate-button" type="submit">Aprēķināt cenu</button>
			</form>

			<aside class="estimate-card" aria-live="polite">
				<p class="eyebrow">Aptuvenās izmaksas</p>
				<div class="estimate-total"><span id="total">{{ number_format($estimate['total'] ?? 0, 2, ',', ' ') }}</span> <small>€</small></div>
				<p class="estimate-note">ar PVN · bez apmalēm un papildu labiekārtošanas</p>
				<div class="estimate-lines">
					<div><span id="paving-name">{{ $selectedPavingName ?? 'Bruģis' }}</span><strong id="paving-total">{{ number_format($estimate['paving'] ?? 0, 2, ',', ' ') }} €</strong></div>
					<div><span>Pamatnes sagatavošana</span><strong id="base-total">{{ number_format($estimate['base'] ?? 0, 2, ',', ' ') }} €</strong></div>
					<div class="optional-line" id="removal-line" @if (!($estimate['removal'] ?? 0)) hidden @endif><span>Demontāža</span><strong id="removal-total">{{ number_format($estimate['removal'] ?? 0, 2, ',', ' ') }} €</strong></div>
				</div>
			</aside>
		</section>
	</main>
</x-layout>
