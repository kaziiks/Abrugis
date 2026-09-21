<x-layout title="Abrugis" body-class="site-shell">
	<main>
		<section class="hero" id="par-mums">
			<div class="hero-copy">
				<p class="eyebrow">{{ __('Paving') }} · {{ __('In Riga and all Latvia') }}</p>
				<h1>Aigara<br><em>Bruģēšanas darbi</em></h1>
				<p class="lead">{{ __('Paving solutions for yards, terraces and driveways. From the first sketch to the last stone.') }}</p>
				<div class="hero-actions">
					<a href="{{ route('form') }}" class="primary-btn">{{ __('Contact') }}</a>
					<a href="#darbi" class="secondary-btn">{{ __('Portfolio') }}</a>
					<a href="#atsauksmes" class="secondary-btn">{{ __('Reviews') }}</a>
				</div>
			</div>
			<div class="hero-image">
				<div class="hero-stamp">14+<small>{{ __('years of experience') }}</small></div>
			</div>
		</section>

		<section class="section" id="darbi">
			<div class="section-title">
				<div><p class="eyebrow">{{ __('A selection of our work') }}</p><h2>Portfolio</h2></div>
				<span class="count">{{ str_pad($portfolio->count(), 2, '0', STR_PAD_LEFT) }} {{ __('projects') }}</span>
			</div>
			<form class="portfolio-filter" method="GET" action="{{ url('/') }}#darbi">
				<label for="bruga_veids_id">{{ __('Filter by paving type') }}</label>
				<select id="bruga_veids_id" name="bruga_veids_id" onchange="this.form.submit()">
					<option value="">{{ __('All paving types') }}</option>
					@foreach ($brugaVeidi as $brugaVeids)
						<option value="{{ $brugaVeids->id }}" @selected($selectedPavingTypeId === $brugaVeids->id)>{{ $brugaVeids->name }}</option>
					@endforeach
				</select>
				<noscript><button class="secondary-btn" type="submit">{{ __('Filter') }}</button></noscript>
			</form>
			<div class="projects">
				@php
					$fallbackImages = [
						'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=900&q=80',
						'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=900&q=80',
						'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=900&q=80',
					];
				@endphp
				@forelse ($portfolio as $index => $project)
					<article class="project">
						<div class="project-image">
							<img src="{{ $project->bildes->first() ? asset('storage/' . $project->bildes->first()->image_path) : $fallbackImages[$index % count($fallbackImages)] }}" alt="{{ $project->title }}">
						</div>
						<h3>{{ $project->title }}</h3><p>{{ $project->brugaVeids?->name ?? __('Paving type not specified') }} · {{ $project->city }} · {{ $project->area_m2 }} m² · {{ $project->completed_year }}</p><p>{{ $project->description }}</p>
					</article>
				@empty
					<p class="empty-state">{{ __('Portfolio projects will be available here soon.') }}</p>
				@endforelse
			</div>
		</section>

		<section class="section testimonials" id="atsauksmes">
			<div class="section-title">
				<div><p class="eyebrow">{{ __('Customer experience') }}</p><h2>{{ __('Reviews') }}</h2></div>
			</div>
			<div class="testimonial-grid">
				@forelse ($atsauksmes as $atsauksme)
					<article class="testimonial">
						<div class="testimonial-rating" aria-label="{{ $atsauksme->rating }} {{ __('out of 5 stars') }}">{{ str_repeat('★', $atsauksme->rating) }}<span>{{ str_repeat('★', 5 - $atsauksme->rating) }}</span></div>
						<p>“{{ $atsauksme->atsauksme ?: __('Great cooperation and quality work.') }}”</p>
						<strong>{{ $atsauksme->author_name }}</strong>
					</article>
				@empty
					<p class="empty-state">{{ __('Customer reviews will be available here soon.') }}</p>
				@endforelse
			</div>
		</section>

		<section class="section" id="kontakti">
			<a href="{{ route('form') }}">{{ __('Contact us') }}</a>
			<a href="mailto:abrugis@gmail.com">abrugis@gmail.com</a>
		</section>
	</main>
	<footer class="footer"><span>ABRUGIS © {{ date('Y') }}</span><span>{{ __('Paving with purpose.') }}</span></footer>
</x-layout>