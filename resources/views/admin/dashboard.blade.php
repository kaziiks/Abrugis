<x-layout title="{{ __('Admin panel') }} | Abrugis">
    <main class="admin-page">
        <div class="admin-heading">
            <div>
                <p class="eyebrow">{{ __('Management') }}</p>
                <h1>{{ __('Admin panel') }}</h1>
            </div>
            <a class="admin-home-link" href="{{ url('/') }}">{{ __('View website') }}</a>
        </div>

        @if (session('success'))
            <p class="admin-success" role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <ul class="admin-errors" role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($adminSection === 'calendar')
        <section class="admin-section calendar-section">
            <div class="admin-section-heading">
                <div>
                    <p class="eyebrow">{{ __('Planning') }}</p>
                    <h2>{{ __('Consultation calendar') }}</h2>
                </div>
                <div class="calendar-legend" aria-label="{{ __('Consultation statuses') }}">
                    <span><i class="calendar-dot calendar-dot-new"></i>{{ __('New') }}</span>
                    <span><i class="calendar-dot calendar-dot-approved"></i>{{ __('Approved') }}</span>
                    <span><i class="calendar-dot calendar-dot-completed"></i>{{ __('Completed') }}</span>
                </div>
            </div>

            <div class="admin-calendar">
                <div class="calendar-toolbar">
                    <a class="calendar-nav" href="{{ route('admin.dashboard', ['calendar_month' => $calendarMonth->copy()->subMonth()->format('Y-m')]) }}" aria-label="{{ __('Previous month') }}">&larr;</a>
                    <h3>{{ $calendarMonth->translatedFormat('F Y') }}</h3>
                    <a class="calendar-nav" href="{{ route('admin.dashboard', ['calendar_month' => $calendarMonth->copy()->addMonth()->format('Y-m')]) }}" aria-label="{{ __('Next month') }}">&rarr;</a>
                </div>
                <div class="calendar-grid calendar-weekdays">
                    @foreach ([__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')] as $weekday)
                        <span>{{ $weekday }}</span>
                    @endforeach
                </div>
                <div class="calendar-grid calendar-days">
                    @for ($blank = 1; $blank < $calendarMonth->dayOfWeekIso; $blank++)
                        <span class="calendar-day calendar-day-empty"></span>
                    @endfor
                    @for ($day = 1; $day <= $calendarMonth->daysInMonth; $day++)
                        @php
                            $dateKey = $calendarMonth->copy()->day($day)->format('Y-m-d');
                            $dayApplications = $calendarApplications->get($dateKey, collect());
                        @endphp
                        <a class="calendar-day {{ $dayApplications->isNotEmpty() ? 'calendar-day-booked' : '' }}" href="{{ route('admin.applications', ['requested_date' => $dateKey]) }}" aria-label="{{ __('Applications on :date', ['date' => $calendarMonth->copy()->day($day)->format('d.m.Y')]) }}">
                            <strong>{{ $day }}</strong>
                            @foreach ($dayApplications as $application)
                                <span class="calendar-event status-{{ $application->status }}">
                                    {{ $application->client_name }}
                                </span>
                            @endforeach
                        </a>
                    @endfor
                </div>
            </div>
        </section>
        @endif

        @if ($adminSection === 'applications')
        <section class="admin-section">
            <div class="admin-section-heading">
                <div>
                    <p class="eyebrow">{{ __('Client messages') }}</p>
                    <h2>{{ __('Applications inbox') }}</h2>
                    @if ($requestedDate)
                        <p class="inbox-requested-date">{{ __('Showing applications for :date', ['date' => \Illuminate\Support\Carbon::parse($requestedDate)->format('d.m.Y')]) }} · <a href="{{ route('admin.applications') }}">{{ __('Clear date filter') }}</a></p>
                    @endif
                </div>
                <span class="admin-count">{{ $applications->count() }}</span>
            </div>

            <div class="admin-inbox">
                @forelse ($applications as $application)
                    <form id="application-{{ $application->id }}" class="admin-inbox-item" method="POST" action="{{ route('admin.applications.update', $application) }}">
                        @csrf
                        @method('PATCH')
                        <div class="inbox-meta">
                            <strong>{{ $application->client_name }}</strong>
                            <span>{{ $application->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <p><a href="mailto:{{ $application->client_email }}">{{ $application->client_email }}</a> · {{ $application->client_phone }}</p>
                        <p>{{ $application->pavingType?->name ?? __('Paving type not specified') }} · {{ $application->area_m2 ?? '–' }} m²</p>
                        @if ($application->estimate_total !== null)
                            <p>{{ __('Calculator estimate') }}: {{ number_format($application->estimate_total, 2, ',', ' ') }} €</p>
                        @endif
                        @if ($application->requested_date)
                            <p class="inbox-requested-date">{{ __('Consultation date') }}: {{ $application->requested_date->format('d.m.Y') }}</p>
                        @endif
                        <p class="inbox-description">{{ $application->project_description }}</p>
                        <div class="inbox-actions">
                            <select name="status" aria-label="{{ __('Application status') }}">
                                @foreach (['new' => __('New'), 'contacted' => __('Contacted'), 'approved' => __('Approved'), 'completed' => __('Completed'), 'rejected' => __('Rejected')] as $value => $label)
                                    <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="admin-button" type="submit">{{ __('Update') }}</button>
                        </div>
                    </form>
                @empty
                    <p>{{ $requestedDate ? __('No applications for this date.') : __('No new applications.') }}</p>
                @endforelse
            </div>
        </section>
        @endif

        @if ($adminSection === 'portfolio')
        <section class="admin-section admin-portfolio-section">
            <div class="admin-section-heading">
                <div>
					<p class="eyebrow">{{ __('Website content') }}</p>
                    <h2>Portfolio</h2>
                </div>
                <span class="admin-count">{{ $portfolio->count() }}</span>
            </div>

            <form class="admin-form admin-create-form" method="POST" action="{{ route('admin.portfolio.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="admin-form-title">
                    <span class="admin-form-kicker">{{ __('New entry') }}</span>
                    <h3>{{ __('Add project') }}</h3>
                    <p>{{ __('Fill in the basic information to show the project in the website portfolio.') }}</p>
                </div>
                <div class="admin-form-grid">
                    <label>{{ __('Title') }}<input name="title" value="{{ old('title') }}" placeholder="{{ __('For example, private house yard') }}" required></label>
                    <label>{{ __('City') }}<input name="city" value="{{ old('city') }}" placeholder="{{ __('City or municipality') }}" required></label>
                    <label>{{ __('Paving type') }}<select name="paving_type_id" required>
                        <option value="">{{ __('Choose a paving type') }}</option>
                        @foreach ($pavingTypes as $pavingType)
                            <option value="{{ $pavingType->id }}" @selected(old('paving_type_id') == $pavingType->id)>{{ $pavingType->name }}</option>
                        @endforeach
                    </select></label>
                    <label>{{ __('Area m²') }}<input name="area_m2" type="number" min="0" step="0.01" placeholder="0.00"></label>
                    <label>{{ __('Completion year') }}<input name="completed_year" type="number" min="1900" max="2100" placeholder="2026"></label>
                    <label class="admin-form-wide">{{ __('Description') }}<textarea name="description" rows="3" placeholder="{{ __('Short description of the completed work') }}"></textarea></label>
                    <label class="admin-form-wide">{{ __('Project images') }}<input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple><small>{{ __('Up to 10 images, 5 MB each.') }}</small></label>
                </div>
                <button class="admin-button" type="submit">{{ __('Add project') }}</button>
            </form>

            <details class="portfolio-projects" @if ($errors->any()) open @endif>
                <summary class="portfolio-projects-toggle">
                    <span>{{ __('Show portfolio projects') }}</span>
                    <span class="admin-count">{{ $portfolio->count() }}</span>
                </summary>
                <div class="admin-items">
                    @forelse ($portfolio as $project)
                    <form class="admin-item admin-edit-form" method="POST" action="{{ route('admin.portfolio.update', $project) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="admin-item-heading">
                            <div>
                                <span class="admin-form-kicker">{{ __('Project') }} #{{ $project->id }}</span>
                                <h3>{{ $project->title }}</h3>
                            </div>
                            <span class="admin-item-city">{{ $project->city }}</span>
                        </div>
                        <div class="admin-form-grid">
                            <label class="admin-edit-compact-field">{{ __('Title') }}<input name="title" value="{{ $project->title }}" required></label>
                            <label class="admin-edit-compact-field">{{ __('City') }}<input name="city" value="{{ $project->city }}" required></label>
                            <label>{{ __('Paving type') }}<select name="paving_type_id" required>
                                @foreach ($pavingTypes as $pavingType)
                                    <option value="{{ $pavingType->id }}" @selected($project->paving_type_id === $pavingType->id)>{{ $pavingType->name }}</option>
                                @endforeach
                            </select></label>
                            <label>{{ __('Area m²') }}<input name="area_m2" type="number" min="0" step="0.01" value="{{ $project->area_m2 }}"></label>
                            <label>{{ __('Completion year') }}<input name="completed_year" type="number" min="1900" max="2100" value="{{ $project->completed_year }}"></label>
                            <label class="admin-form-wide">{{ __('Description') }}<textarea name="description" rows="3">{{ $project->description }}</textarea></label>
                            <label class="admin-form-wide">{{ __('Add images') }}<input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple><small>{{ __('New images will be added to the existing ones.') }}</small></label>
                        </div>
                        @if ($project->images->isNotEmpty())
                            <div class="admin-image-grid">
                                @foreach ($project->images as $image)
                                    <div class="admin-image-item">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path) ? asset('storage/' . $image->image_path) : asset('images/portfolio-placeholder.svg') }}" alt="{{ $project->title }}">
                                        <button class="admin-delete" type="submit" form="delete-portfolio-image-{{ $image->id }}">{{ __('Delete image') }}</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="admin-item-actions">
                            <button class="admin-button" type="submit">{{ __('Save changes') }}</button>
                            <button class="admin-delete" type="submit" form="delete-portfolio-{{ $project->id }}">{{ __('Delete') }}</button>
                        </div>
                    </form>
                        @foreach ($project->images as $image)
                            <form id="delete-portfolio-image-{{ $image->id }}" method="POST" action="{{ route('admin.portfolio.images.destroy', $image) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                        <form id="delete-portfolio-{{ $project->id }}" method="POST" action="{{ route('admin.portfolio.destroy', $project) }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    @empty
                        <p>{{ __('There are no portfolio projects yet.') }}</p>
                    @endforelse
                </div>
            </details>
        </section>
        @endif
    </main>
</x-layout>