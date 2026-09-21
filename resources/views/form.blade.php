<x-layout title="{{ __('Application') }} | Abrugis">
    <main class="form-page">
        <section class="form-shell">
            <div class="form-intro">
                <p class="eyebrow">{{ __('Application') }}</p>
                <h1>{{ __('Create an application') }}</h1>
                <p>{{ __('Fill in the form and we will contact you about your project options.') }}</p>
            </div>

            @if ($errors->any())
                <ul class="error-list" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form class="application-form" method="POST" action="{{ route('form.store') }}">
                @csrf

                <div class="form-grid">
                    <label for="client_name">{{ __('Name') }}
                        <input id="client_name" name="client_name" type="text" value="{{ old('client_name', auth()->user()->name) }}" required autofocus>
                    </label>

                    <label for="client_email">{{ __('Email') }}
                        <input id="client_email" name="client_email" type="email" value="{{ old('client_email', auth()->user()->email) }}" required>
                    </label>

                    <label for="client_phone">{{ __('Phone number') }}
                        <input id="client_phone" name="client_phone" type="tel" value="{{ old('client_phone') }}" required>
                    </label>

                    <label for="bruga_veids_id">{{ __('Preferred paving type') }}
                        <select id="bruga_veids_id" name="bruga_veids_id">
                            <option value="">{{ __('Choose a paving type') }}</option>
                            @foreach ($brugaVeidi as $brugaVeids)
                                <option value="{{ $brugaVeids->id }}" @selected(old('bruga_veids_id') == $brugaVeids->id)>
                                    {{ $brugaVeids->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label for="area_m2">{{ __('Approximate area m²') }}
                        <input id="area_m2" name="area_m2" type="number" min="1" step="0.01" value="{{ old('area_m2') }}">
                    </label>
                </div>

                <label for="project_description">{{ __('Project description') }}
                    <textarea id="project_description" name="project_description" rows="6" required>{{ old('project_description') }}</textarea>
                </label>

                <button type="submit">{{ __('Send application') }}</button>
            </form>

            @if (session('success'))
                <p class="success-note" role="status">{{ session('success') }}</p>
            @endif
        </section>

        <section class="form-shell applications-shell">
            <div class="form-intro">
                <p class="eyebrow">{{ __('Client area') }}</p>
                <h2>{{ __('My applications') }}</h2>
                <p>{{ __('Here you can see your application status and submitted information.') }}</p>
            </div>

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
