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

        <section class="admin-section">
            <div class="admin-section-heading">
                <div>
                    <p class="eyebrow">{{ __('Client messages') }}</p>
                    <h2>{{ __('Applications inbox') }}</h2>
                </div>
                <span class="admin-count">{{ $pieteikumi->count() }}</span>
            </div>

            <div class="admin-inbox">
                @forelse ($pieteikumi as $pieteikums)
                    <form class="admin-inbox-item" method="POST" action="{{ route('admin.pieteikumi.update', $pieteikums) }}">
                        @csrf
                        @method('PATCH')
                        <div class="inbox-meta">
                            <strong>{{ $pieteikums->client_name }}</strong>
                            <span>{{ $pieteikums->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <p><a href="mailto:{{ $pieteikums->client_email }}">{{ $pieteikums->client_email }}</a> · {{ $pieteikums->client_phone }}</p>
                        <p>{{ $pieteikums->pavingType?->name ?? __('Paving type not specified') }} · {{ $pieteikums->area_m2 ?? '–' }} m²</p>
                        <p class="inbox-description">{{ $pieteikums->project_description }}</p>
                        <div class="inbox-actions">
                            <select name="status" aria-label="{{ __('Application status') }}">
                                @foreach (['new' => __('New'), 'contacted' => __('Contacted'), 'approved' => __('Approved'), 'completed' => __('Completed'), 'rejected' => __('Rejected')] as $value => $label)
                                    <option value="{{ $value }}" @selected($pieteikums->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="admin-button" type="submit">{{ __('Update') }}</button>
                        </div>
                    </form>
                @empty
                    <p>{{ __('No new applications.') }}</p>
                @endforelse
            </div>
        </section>

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
                    <label>{{ __('Paving type') }}<select name="bruga_veids_id" required>
                        <option value="">{{ __('Choose a paving type') }}</option>
                        @foreach ($brugaVeidi as $brugaVeids)
                            <option value="{{ $brugaVeids->id }}" @selected(old('bruga_veids_id') == $brugaVeids->id)>{{ $brugaVeids->name }}</option>
                        @endforeach
                    </select></label>
                    <label>{{ __('Area m²') }}<input name="area_m2" type="number" min="0" step="0.01" placeholder="0.00"></label>
                    <label>{{ __('Completion year') }}<input name="completed_year" type="number" min="1900" max="2100" placeholder="2026"></label>
                    <label class="admin-form-wide">{{ __('Description') }}<textarea name="description" rows="3" placeholder="{{ __('Short description of the completed work') }}"></textarea></label>
                    <label class="admin-form-wide">{{ __('Project images') }}<input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple><small>{{ __('Up to 10 images, 5 MB each.') }}</small></label>
                </div>
                <button class="admin-button" type="submit">{{ __('Add project') }}</button>
            </form>

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
                            <label>{{ __('Paving type') }}<select name="bruga_veids_id" required>
                                @foreach ($brugaVeidi as $brugaVeids)
                                    <option value="{{ $brugaVeids->id }}" @selected($project->bruga_veids_id === $brugaVeids->id)>{{ $brugaVeids->name }}</option>
                                @endforeach
                            </select></label>
                            <label>{{ __('Area m²') }}<input name="area_m2" type="number" min="0" step="0.01" value="{{ $project->area_m2 }}"></label>
                            <label>{{ __('Completion year') }}<input name="completed_year" type="number" min="1900" max="2100" value="{{ $project->completed_year }}"></label>
                            <label class="admin-form-wide">{{ __('Description') }}<textarea name="description" rows="3">{{ $project->description }}</textarea></label>
                            <label class="admin-form-wide">{{ __('Add images') }}<input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple><small>{{ __('New images will be added to the existing ones.') }}</small></label>
                        </div>
                        @if ($project->bildes->isNotEmpty())
                            <div class="admin-image-grid">
                                @foreach ($project->bildes as $bilde)
                                    <div class="admin-image-item">
                                        <img src="{{ asset('storage/' . $bilde->image_path) }}" alt="{{ $project->title }}">
                                        <button class="admin-delete" type="submit" form="delete-portfolio-image-{{ $bilde->id }}">{{ __('Delete image') }}</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="admin-item-actions">
                            <button class="admin-button" type="submit">{{ __('Save changes') }}</button>
                            <button class="admin-delete" type="submit" form="delete-portfolio-{{ $project->id }}">{{ __('Delete') }}</button>
                        </div>
                    </form>
                    @foreach ($project->bildes as $bilde)
                        <form id="delete-portfolio-image-{{ $bilde->id }}" method="POST" action="{{ route('admin.portfolio.bildes.destroy', $bilde) }}">
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
        </section>
    </main>
</x-layout>