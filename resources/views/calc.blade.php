<x-layout title="{{ __('Paving price calculator') }} | Abrugis">
	<main class="calculator-page">
		<section class="calculator-intro">
			<h1>{{ __('Discover your project price.') }}</h1>
			<p class="calculator-lead">{{ __('Enter the area, choose the paving type and base solution. The final price may vary depending on the site and preparation work.') }}</p>
		</section>

		<section class="calculator-grid" aria-label="{{ __('Paving price calculator') }}">
			<form class="calculator-form" id="calculator-form" method="POST" action="{{ route('calc.calculate') }}">
				@csrf
				@if ($errors->any())
					<ul class="admin-errors" role="alert">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				@endif
				<div class="form-heading">
					<h2>{{ __('Project parameters') }}</h2>
				</div>

				<section class="area-helper" aria-labelledby="area-helper-title">
					<div>
						<p class="eyebrow" id="area-helper-title">{{ __('Calculate area from dimensions') }}</p>
						<p class="area-helper-description">{{ __('Enter length and width in meters to calculate an area.') }}</p>
					</div>
					<div class="area-helper-fields">
						<label for="area-length">{{ __('Length, m') }}<input id="area-length" type="number" min="0.1" step="0.1" inputmode="decimal"></label>
						<label for="area-width">{{ __('Width, m') }}<input id="area-width" type="number" min="0.1" step="0.1" inputmode="decimal"></label>
					</div>
					<div class="area-helper-footer">
						<p id="area-helper-result" role="status" aria-live="polite"
							data-empty-message="{{ __('Enter both measurements to see the area.') }}"
							data-range-message="{{ __('Area must be between 1 and 100,000 m².') }}"
							data-area-template="{{ __('Calculated area: :area m²', ['area' => ':area']) }}">{{ __('Enter both measurements to see the area.') }}</p>
						<button class="secondary-btn" id="use-calculated-area" type="button" disabled>{{ __('Use calculated area') }}</button>
					</div>
				</section>

				<label for="area">{{ __('Area, m²') }}</label>
				<div class="input-with-unit">
					<input id="area" name="area" type="number" min="1" max="100000" step="0.1" value="{{ old('area') }}" required>
					<span>m²</span>
				</div>

				<label for="paving">{{ __('Paving type') }}</label>
				<select id="paving" name="paving_id" required>
					@forelse ($pavingTypes as $pavingType)
						<option value="{{ $pavingType->id }}" data-price="{{ $pavingType->price_per_m2 }}" data-name="{{ $pavingType->name }}" @selected(old('paving_id', $selectedPavingId ?? null) == $pavingType->id)>{{ $pavingType->name }} · {{ number_format($pavingType->price_per_m2, 2, ',', ' ') }} €/m²</option>
					@empty
						<option value="" disabled selected>{{ __('No paving types are currently available.') }}</option>
					@endforelse
				</select>

				<fieldset>
					<legend>{{ __('Base preparation') }}</legend>
					<label class="choice">
						<input type="radio" name="base" value="standard" data-price="{{ $baseOptions['standard'] }}" @checked(old('base', $selectedBase ?? 'standard') === 'standard')>
						<span><strong>{{ __('Standard') }}</strong><small>{{ __('Sand, crushed stone and compaction') }}</small></span>
						<b>{{ number_format($baseOptions['standard'], 2, ',', ' ') }} €/m²</b>
					</label>
					<label class="choice">
						<input type="radio" name="base" value="reinforced" data-price="{{ $baseOptions['reinforced'] }}" @checked(old('base', $selectedBase ?? 'standard') === 'reinforced')>
						<span><strong>{{ __('Reinforced') }}</strong><small>{{ __('Deeper base for higher loads') }}</small></span>
						<b>{{ number_format($baseOptions['reinforced'], 2, ',', ' ') }} €/m²</b>
					</label>
				</fieldset>

				<label class="checkbox-choice">
					<input id="removal" name="removal" type="checkbox" value="1" data-price="{{ $removalPrice }}" @checked(old('removal', $removalSelected ?? false))>
					<span><strong>{{ __('Old surface removal') }}</strong><small>+{{ number_format($removalPrice, 2, ',', ' ') }} €/m²</small></span>
				</label>

				<button class="calculate-button" type="submit" @disabled($pavingTypes->isEmpty())>{{ __('Calculate price') }}</button>
			</form>

			<aside class="estimate-card" aria-live="polite">
				<p class="eyebrow">{{ __('Estimated cost') }}</p>
				<div class="estimate-total"><span id="total">{{ number_format($estimate['total'] ?? 0, 2, ',', ' ') }}</span> <small>€</small></div>
				<p class="estimate-note">{{ __('excluding curbs and additional landscaping') }}</p>
				<div class="estimate-lines">
					<div><span id="paving-name">{{ $selectedPavingName ?? __('Paving') }}</span><strong id="paving-total">{{ number_format($estimate['paving_total'] ?? 0, 2, ',', ' ') }} €</strong></div>
					<div><span>{{ __('Base preparation') }}</span><strong id="base-total">{{ number_format($estimate['base_total'] ?? 0, 2, ',', ' ') }} €</strong></div>
					<div class="optional-line" id="removal-line" @if (!($estimate['removal_total'] ?? 0)) hidden @endif><span>{{ __('Removal') }}</span><strong id="removal-total">{{ number_format($estimate['removal_total'] ?? 0, 2, ',', ' ') }} €</strong></div>
				</div>
				@if (isset($estimate))
					<a class="primary-btn estimate-application-link" href="{{ route('form') }}">{{ __('Use this estimate in an application') }}</a>
				@endif
			</aside>
		</section>
	</main>
</x-layout>
