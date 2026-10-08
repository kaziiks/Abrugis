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

            @if ($applications->isEmpty())
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
                    @foreach ($applications as $application)
                        <article class="application-item">
                            <div class="application-item-heading">
                                <div>
                                    <span class="application-date">{{ $application->created_at->format('d.m.Y H:i') }}</span>
                                    <h3>{{ $application->project_description }}</h3>
                                </div>
                                <span class="status-badge status-{{ $application->status }}">{{ $statusLabels[$application->status] ?? $application->status }}</span>
                            </div>
                            <div class="application-meta">
                                <span>{{ $application->pavingType?->name ?? __('Paving type not specified') }}</span>
                                <span>{{ $application->area_m2 ? $application->area_m2 . ' m²' : __('Area not specified') }}</span>
                                @if ($application->requested_date)
                                    <span>{{ __('Consultation date') }}: {{ $application->requested_date->format('d.m.Y') }}</span>
                                @endif
                            </div>
                            @if ($application->estimate_total !== null)
                                <p class="application-review-note">{{ __('Calculator estimate') }}: {{ number_format($application->estimate_total, 2, ',', ' ') }} €</p>
                            @endif
                            @if ($application->review)
                                <p class="application-review-note">{{ __('Review submitted') }} · {{ $application->review->rating }}/5 {{ __('stars') }}</p>
                            @elseif ($application->status === 'completed')
                                <p class="application-review-note application-review-pending">{{ __('You can leave a review for this application below.') }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($applications->contains(fn ($application) => $application->status === 'completed' && ! $application->review))
            <section class="form-shell review-shell">
                <div class="form-intro">
                    <p class="eyebrow">{{ __('Your experience') }}</p>
                    <h2>{{ __('Leave a review') }}</h2>
                    <p>{{ __('Share your experience about a completed project.') }}</p>
                </div>
                <form class="application-form" method="POST" action="{{ route('reviews.store') }}">
                    @csrf
                    <label for="application_id">{{ __('Completed project') }}
                        <select id="application_id" name="application_id" required>
                            @foreach ($applications as $application)
                                @if ($application->status === 'completed' && ! $application->review)
                                    <option value="{{ $application->id }}">{{ $application->project_description }}</option>
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
                    <label for="review">{{ __('Review') }}
                        <textarea id="review" name="review" rows="4" maxlength="2000" placeholder="{{ __('How was your experience?') }}"></textarea>
                    </label>
                    <button type="submit">{{ __('Send review') }}</button>
                </form>
            </section>
        @endif
    </main>
</x-layout>
