<x-layout title="{{ __('My applications') }} | Abrugis">
    <main class="form-page">
        <section id="my-applications" class="form-shell applications-shell">
            <div class="form-intro">
                <p class="eyebrow">{{ __('Client area') }}</p>
                <h1>{{ __('My applications') }}</h1>
                <p>{{ __('Here you can see your application status and submitted information.') }}</p>
            </div>

            @if (session('success'))
                <p class="success-note" role="status">{{ session('success') }}</p>
            @endif

            @if ($pieteikumi->isEmpty())
                <p class="empty-state">{{ __('No applications have been sent yet.') }}</p>
            @else
                <div class="application-list">
                    @php
                        $statusLabels = [
                            'new' => __('New'),
                            'contacted' => __('Contacted'),
                            'approved' => __('Approved'),
                            'completed' => __('Completed'),
                            'rejected' => __('Rejected'),
                        ];
                    @endphp
                    @foreach ($pieteikumi as $pieteikums)
                        <article class="application-item">
                            <div class="application-item-heading">
                                <div>
                                    <span class="application-date">{{ $pieteikums->created_at->format('d.m.Y H:i') }}</span>
                                    <h3>{{ $pieteikums->project_description }}</h3>
                                </div>
                                <span class="status-badge status-{{ $pieteikums->status }}">{{ $statusLabels[$pieteikums->status] ?? $pieteikums->status }}</span>
                            </div>
                            <div class="application-meta">
                                <span>{{ $pieteikums->pavingType?->name ?? __('Paving type not specified') }}</span>
                                <span>{{ $pieteikums->area_m2 ? $pieteikums->area_m2 . ' m²' : __('Area not specified') }}</span>
                                @if ($pieteikums->requested_date)
                                    <span>{{ __('Preferred date') }}: {{ $pieteikums->requested_date->format('d.m.Y') }}</span>
                                @endif
                            </div>
                            @if ($pieteikums->review)
                                <p class="application-review-note">{{ __('Review submitted') }} · {{ $pieteikums->review->rating }}/5 {{ __('stars') }}</p>
                            @elseif ($pieteikums->status === 'completed')
                                <p class="application-review-note application-review-pending">{{ __('You can leave a review for this application below.') }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($pieteikumi->contains(fn ($pieteikums) => $pieteikums->status === 'completed' && ! $pieteikums->review))
            <section class="form-shell review-shell">
                <div class="form-intro">
                    <p class="eyebrow">{{ __('Your experience') }}</p>
                    <h2>{{ __('Leave a review') }}</h2>
                    <p>{{ __('Share your experience about a completed project.') }}</p>
                </div>
                <form class="application-form" method="POST" action="{{ route('atsauksmes.store') }}">
                    @csrf
                    <label for="pieteikums_id">{{ __('Completed project') }}
                        <select id="pieteikums_id" name="pieteikums_id" required>
                            @foreach ($pieteikumi as $pieteikums)
                                @if ($pieteikums->status === 'completed' && ! $pieteikums->review)
                                    <option value="{{ $pieteikums->id }}">{{ $pieteikums->project_description }}</option>
                                @endif
                            @endforeach
                        </select>
                    </label>
                    <label for="rating">{{ __('Rating') }}
                        <select id="rating" name="rating" required>
                            <option value="5">5 zvaigznes</option>
                            <option value="4">4 zvaigznes</option>
                            <option value="3">3 zvaigznes</option>
                            <option value="2">2 zvaigznes</option>
                            <option value="1">1 zvaigzne</option>
                        </select>
                    </label>
                    <label for="atsauksme">{{ __('Review') }}
                        <textarea id="atsauksme" name="atsauksme" rows="4" maxlength="2000" placeholder="{{ __('How was your experience?') }}"></textarea>
                    </label>
                    <button type="submit">{{ __('Send review') }}</button>
                </form>
            </section>
        @endif
    </main>
</x-layout>
