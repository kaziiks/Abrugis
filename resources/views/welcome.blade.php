<x-layout title="Abrugis" body-class="site-shell">
	<main>
		<section class="hero" id="par-mums">
			<div class="hero-copy">
				<p class="eyebrow">Bruģēšana · Rīgā un visā Latvija</p>
				<h1>Aigara<br><em>Bruģēšanas darbi</em></h1>
				<p class="lead">Veidojam ilgmūžīgus bruģa risinājumus pagalmiem, terasēm un piebraucamiem ceļiem. No pirmās skices līdz pēdējam akmenim.</p>
			</div>
			<div class="hero-image">
				<div class="hero-stamp">14+<small>gadi pieredzes</small></div>
			</div>
		</section>

		<section class="section" id="darbi">
			<div class="section-title">
				<div><p class="eyebrow">Izlase no mūsu darbiem</p><h2>Portfolio</h2></div>
				<span class="count">{{ str_pad($portfolio->count(), 2, '0', STR_PAD_LEFT) }} projekti</span>
			</div>
			<div class="projects">
				@php
					$fallbackImages = [
						asset('images/fallback1.jpg'),
						asset('images/fallback2.jpg'),
						asset('images/fallback3.jpg'),
					];
				@endphp
				@forelse ($portfolio as $index => $project)
					<article class="project">
						<div class="project-image">
							<img src="{{ $fallbackImages[$index % count($fallbackImages)] }}" alt="{{ $project->title }}">
						</div>
						<h3>{{ $project->title }}</h3><p>{{ $project->city }} · {{ $project->area_m2 }} m² · {{ $project->completed_year }}</p><p>{{ $project->description }}</p>
					</article>
				@empty
					<p class="empty-state">Portfolio projekti drīzumā būs apskatāmi šeit.</p>
				@endforelse
			</div>
		</section>
		<br><br>

		<section class="section" id="kontakti">
			<p class="eyebrow">Ir projekts prātā?</p><h2>Parunāsim par<br><em>jūsu pagalmu.</em></h2>
			<a href="{{ route('form') }}">Sazināties ar mums</a>
			<a>example@gmail.com</a>
		</section>
	</main>
	<footer class="footer"><span>ABRUGIS © {{ date('Y') }}</span><span>Bruģējam ar nodomu.</span></footer>
</x-layout>