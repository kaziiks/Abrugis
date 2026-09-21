<x-layout title="{{ __('Paving price calculator') }} | Abrugis">
	<main class="calculator-page">
		<section class="calculator-intro">
			<h1>{{ __('Discover your project price.') }}</h1>
			<p class="calculator-lead">{{ __('Enter the area, choose the paving type and base solution. The final price may vary depending on the site and preparation work.') }}</p>
		</section>

		<section class="calculator-grid" aria-label="{{ __('Paving price calculator') }}">
			<form class="calculator-form" id="calculator-form" method="POST" action="{{ route('calc.calculate') }}">
				@csrf
				<div class="form-heading">
					<h2>{{ __('Project parameters') }}</h2>
				</div>

				<label for="area">{{ __('Area, m²') }}</label>
				<div class="input-with-unit">
					<input id="area" name="area" type="number" min="1" max="100000" step="0.1" required>
					<span>m²</span>
				</div>

				<label for="paving">{{ __('Paving type') }}</label>
				<select id="paving" name="paving">
					@forelse ($brugaVeidi as $brugaVeids)
						<option value="{{ $brugaVeids->price_per_m2 }}" data-name="{{ $brugaVeids->name }}" @selected(old('paving') == $brugaVeids->price_per_m2)>{{ $brugaVeids->name }} · {{ number_format($brugaVeids->price_per_m2, 2, ',', ' ') }} €/m²</option>
					@empty
						<option value="25" data-name="{{ __('Concrete pavers') }}" @selected(old('paving') == 25)>{{ __('Concrete pavers') }} · 25,00 €/m²</option>
						<option value="45" data-name="{{ __('Granite paving stone') }}" @selected(old('paving') == 45)>{{ __('Granite paving stone') }} · 45,00 €/m²</option>
					@endforelse
				</select>

				<fieldset>
					<legend>{{ __('Base preparation') }}</legend>
					<label class="choice">
						<input type="radio" name="base" value="18" @checked(old('base', 18) == 18)>
						<span><strong>{{ __('Standard') }}</strong><small>{{ __('Sand, crushed stone and compaction') }}</small></span>
						<b>18 €/m²</b>
					</label>
					<label class="choice">
						<input type="radio" name="base" value="28" @checked(old('base') == 28)>
						<span><strong>{{ __('Reinforced') }}</strong><small>{{ __('Deeper base for higher loads') }}</small></span>
						<b>28 €/m²</b>
					</label>
				</fieldset>

				<label class="checkbox-choice">
					<input id="removal" name="removal" type="checkbox" value="8" @checked(old('removal'))>
					<span><strong>{{ __('Old surface removal') }}</strong><small>+8 €/m²</small></span>
				</label>

				<button class="calculate-button" type="submit">{{ __('Calculate price') }}</button>
			</form>

			<aside class="estimate-card" aria-live="polite">
				<p class="eyebrow">{{ __('Estimated cost') }}</p>
				<div class="estimate-total"><span id="total">{{ number_format($estimate['total'] ?? 0, 2, ',', ' ') }}</span> <small>€</small></div>
				<p class="estimate-note">{{ __('incl. VAT · excluding curbs and additional landscaping') }}</p>
				<div class="estimate-lines">
					<div><span id="paving-name">{{ $selectedPavingName ?? __('Paving') }}</span><strong id="paving-total">{{ number_format($estimate['paving'] ?? 0, 2, ',', ' ') }} €</strong></div>
					<div><span>{{ __('Base preparation') }}</span><strong id="base-total">{{ number_format($estimate['base'] ?? 0, 2, ',', ' ') }} €</strong></div>
					<div class="optional-line" id="removal-line" @if (!($estimate['removal'] ?? 0)) hidden @endif><span>{{ __('Removal') }}</span><strong id="removal-total">{{ number_format($estimate['removal'] ?? 0, 2, ',', ' ') }} €</strong></div>
				</div>
			</aside>
		</section>
	</main>
</x-layout>
