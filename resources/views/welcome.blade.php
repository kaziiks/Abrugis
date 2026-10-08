<x-layout title="Abrugis" body-class="site-shell">
	<main>
		<section class="hero" id="about">
			<div class="hero-copy">
				<p class="eyebrow">{{ __('Paving') }} · {{ __('In Riga and all Latvia') }}</p>
				<h1>Aigara<br><em>Bruģēšanas darbi</em></h1>
				<p class="lead">{{ __('Paving solutions for yards, terraces and driveways. From the first sketch to the last stone.') }}</p>
				<div class="hero-actions">
					<a href="{{ route('form') }}" class="primary-btn">{{ __('Create an application') }}</a>
					<a href="#projects" class="secondary-btn">{{ __('Portfolio') }}</a>
					<a href="#reviews" class="secondary-btn">{{ __('Reviews') }}</a>
				</div>
			</div>
			<div class="hero-image">
				<div class="hero-stamp">14+<small>{{ __('years of experience') }}</small></div>
			</div>
		</section>

		<section class="section" id="projects">
			<div class="section-title">
				<div><p class="eyebrow">{{ __('A selection of our work') }}</p><h2>Portfolio</h2></div>
				<span class="count">{{ str_pad($portfolio->count(), 2, '0', STR_PAD_LEFT) }} {{ __('projects') }}</span>
			</div>
			<form class="portfolio-filter" method="GET" action="{{ url('/') }}#projects">
				<label for="paving_type_id">{{ __('Filter by paving type') }}</label>
				<select id="paving_type_id" name="paving_type_id" onchange="this.form.submit()">
					<option value="">{{ __('All paving types') }}</option>
					@foreach ($pavingTypes as $pavingType)
						<option value="{{ $pavingType->id }}" @selected($selectedPavingTypeId === $pavingType->id)>{{ $pavingType->name }}</option>
					@endforeach
				</select>
				<noscript><button class="secondary-btn" type="submit">{{ __('Filter') }}</button></noscript>
			</form>
			<div class="projects">
				@forelse ($portfolio as $index => $project)
					<article class="project">
						<div class="project-image">
							<img src="{{ $project->images->isNotEmpty() ? asset('storage/' . $project->images->first()->image_path) : asset('images/portfolio-placeholder.svg') }}" alt="{{ $project->title }}">
						</div>
						<h3>{{ $project->title }}</h3><p>{{ $project->pavingType?->name ?? __('Paving type not specified') }} · {{ $project->city }} · {{ $project->area_m2 }} m² · {{ $project->completed_year }}</p><p>{{ $project->description }}</p>
					</article>
				@empty
					<p class="empty-state">{{ __('Portfolio projects will be available here soon.') }}</p>
				@endforelse
			</div>
		</section>

		<section class="section compare-section" id="compare">
			<div class="section-title">
				<div><p class="eyebrow">Demo visualizer</p><h2>Pirms / Pēc</h2></div>
			</div>

			<div class="compare-toolbar">
				<div class="compare-filters" aria-label="Bruģa tips">
					<button class="compare-filter is-active" type="button" data-filter="betons">Betons</button>
					<button class="compare-filter" type="button" data-filter="klinkers">Klinkers</button>
					<button class="compare-filter" type="button" data-filter="granits">Granīts</button>
				</div>
			</div>

			<div class="compare-card" id="compare-card">
				<div class="compare-layer compare-layer-before">
					<img id="compare-before" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('pirmsunpec/pirmsbetona.jpg') ? asset('storage/pirmsunpec/pirmsbetona.jpg') : asset('images/portfolio-placeholder.svg') }}" alt="Pirms attēls" draggable="false">
				</div>

				<div class="compare-layer compare-layer-after">
					<img id="compare-after" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('pirmsunpec/pecbetona.jpg') ? asset('storage/pirmsunpec/pecbetona.jpg') : asset('images/portfolio-placeholder.svg') }}" alt="Pēc attēls" draggable="false">
				</div>
			</div>
			<div class="compare-toggle" role="group" aria-label="Attēlu salīdzinājums">
				<button class="compare-toggle-option is-active" type="button" data-compare-state="before" aria-controls="compare-card" aria-pressed="true">Pirms</button>
				<button class="compare-toggle-option" type="button" data-compare-state="after" aria-controls="compare-card" aria-pressed="false">Pēc</button>
			</div>
		</section>

		<section class="section testimonials" id="reviews">
			<div class="section-title">
				<div><p class="eyebrow">{{ __('Customer experience') }}</p><h2>{{ __('Reviews') }}</h2></div>
			</div>
			<div class="testimonial-grid">
				@forelse ($reviews as $review)
					<article class="testimonial">
						<div class="testimonial-rating" aria-label="{{ $review->rating }} {{ __('out of 5 stars') }}">{{ str_repeat('★', $review->rating) }}<span>{{ str_repeat('★', 5 - $review->rating) }}</span></div>
						@if ($review->review)
							<p>“{{ $review->review }}”</p>
						@endif
						<strong>{{ $review->author_name }}</strong>
					</article>
				@empty
					<p class="empty-state">{{ __('Customer reviews will be available here soon.') }}</p>
				@endforelse
			</div>
		</section>

		<section class="section trust-section" id="how-we-work">
			<div class="section-title">
				<div><p class="eyebrow">{{ __('From first conversation to finished surface') }}</p><h2>{{ __('How we work') }}</h2></div>
			</div>
			<ol class="trust-steps">
				<li><span class="trust-step-number">01</span><h3>{{ __('Tell us about your project') }}</h3><p>{{ __('Share the location, approximate area and your preferred timing.') }}</p></li>
				<li><span class="trust-step-number">02</span><h3>{{ __('Assess the site') }}</h3><p>{{ __('We clarify the existing surface, access and preparation the site needs.') }}</p></li>
				<li><span class="trust-step-number">03</span><h3>{{ __('Agree the offer') }}</h3><p>{{ __('We confirm the scope, materials, price and schedule before work begins.') }}</p></li>
				<li><span class="trust-step-number">04</span><h3>{{ __('Prepare and pave') }}</h3><p>{{ __('We carry out the agreed preparation and paving work.') }}</p></li>
			</ol>
			<div class="trust-details">
				<article>
					<p class="eyebrow">{{ __('Warranty terms') }}</p>
					<p>{{ __('The warranty period and conditions depend on the agreed work and are stated in the offer before work begins.') }}</p>
				</article>
				<article>
					<p class="eyebrow">{{ __('Materials for your project') }}</p>
					<p>{{ __('We select paving and base preparation for the site, expected load and the look you want. We agree the final choice with you.') }}</p>
				</article>
			</div>
			<div class="trust-faq">
				<h3>{{ __('Frequently asked questions') }}</h3>
				<details>
					<summary>{{ __('What affects the project price?') }}</summary>
					<p>{{ __('The area, paving type, condition of the existing base and any removal or additional preparation work. The calculator gives an estimate; we confirm the offer after discussing the site.') }}</p>
				</details>
				<details>
					<summary>{{ __('Do you work outside Riga?') }}</summary>
					<p>{{ __('Yes, we work in Riga and across Latvia.') }}</p>
				</details>
				<details>
					<summary>{{ __('What should I include in an application?') }}</summary>
					<p>{{ __('Tell us the project location, approximate area, preferred paving type and when you would like the work done.') }}</p>
				</details>
			</div>
		</section>

		<section class="section" id="contact">
			<div class="contact-copy">
				<p class="eyebrow">{{ __('Contact us') }}</p>
				<h2>{{ __('Ready to start?') }}</h2>
				<p>{{ __('Tell us about your yard and we will help plan the next step.') }}</p>
			</div>
			<div class="contact-actions">
				<a href="{{ route('form') }}" class="primary-btn">{{ __('Create an application') }}</a>
				<a href="mailto:abrugis@gmail.com" class="contact-email">abrugis@gmail.com</a>
			</div>
		</section>
	</main>
	<footer class="footer"><span>ABRUGIS © {{ date('Y') }}</span><span>{{ __('Paving with purpose.') }}</span></footer>
</x-layout>