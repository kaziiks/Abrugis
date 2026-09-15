<x-layout title="Admin panelis | Abrugis">
    <main class="admin-page">
        <div class="admin-heading">
            <div>
                <p class="eyebrow">Pārvaldība</p>
                <h1>Admin panelis</h1>
            </div>
            <a class="admin-home-link" href="{{ url('/') }}">Skatīt mājaslapu</a>
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
                    <p class="eyebrow">Klientu ziņas</p>
                    <h2>Pieteikumu inbox</h2>
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
                        <p>{{ $pieteikums->pavingType?->name ?? 'Bruģa veids nav norādīts' }} · {{ $pieteikums->area_m2 ?? '–' }} m²</p>
                        <p class="inbox-description">{{ $pieteikums->project_description }}</p>
                        <div class="inbox-actions">
                            <select name="status" aria-label="Pieteikuma statuss">
                                @foreach (['new' => 'Jauns', 'contacted' => 'Sazināts', 'approved' => 'Apstiprināts', 'completed' => 'Pabeigts', 'rejected' => 'Noraidīts'] as $value => $label)
                                    <option value="{{ $value }}" @selected($pieteikums->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input name="admin_notes" value="{{ $pieteikums->admin_notes }}" placeholder="Piezīmes">
                            <button class="admin-button" type="submit">Atjaunināt</button>
                        </div>
                    </form>
                @empty
                    <p>Jaunu pieteikumu nav.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-section admin-portfolio-section">
            <div class="admin-section-heading">
                <div>
                    <p class="eyebrow">Mājaslapas saturs</p>
                    <h2>Portfolio</h2>
                </div>
                <span class="admin-count">{{ $portfolio->count() }}</span>
            </div>

            <form class="admin-form admin-create-form" method="POST" action="{{ route('admin.portfolio.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="admin-form-title">
                    <span class="admin-form-kicker">Jauns ieraksts</span>
                    <h3>Pievienot projektu</h3>
                    <p>Aizpildi pamatinformāciju, lai projekts parādītos mājaslapas portfolio.</p>
                </div>
                <div class="admin-form-grid">
                    <label>Nosaukums<input name="title" value="{{ old('title') }}" placeholder="Piemēram, Privātmājas pagalms" required></label>
                    <label>Pilsēta<input name="city" value="{{ old('city') }}" placeholder="Pilsēta vai novads" required></label>
                    <label>Bruģa veids<select name="bruga_veids_id" required>
                        <option value="">Izvēlies bruģa veidu</option>
                        @foreach ($brugaVeidi as $brugaVeids)
                            <option value="{{ $brugaVeids->id }}" @selected(old('bruga_veids_id') == $brugaVeids->id)>{{ $brugaVeids->name }}</option>
                        @endforeach
                    </select></label>
                    <label>Platība m²<input name="area_m2" type="number" min="0" step="0.01" placeholder="0.00"></label>
                    <label>Pabeigšanas gads<input name="completed_year" type="number" min="1900" max="2100" placeholder="2026"></label>
                    <label class="admin-form-wide">Apraksts<textarea name="description" rows="3" placeholder="Īss apraksts par paveikto darbu"></textarea></label>
                    <label class="admin-form-wide">Projekta bildes<input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple><small>Var pievienot līdz 10 bildēm, katru līdz 5 MB.</small></label>
                </div>
                <button class="admin-button" type="submit">Pievienot projektu</button>
            </form>

            <div class="admin-items">
                @forelse ($portfolio as $project)
                    <form class="admin-item admin-edit-form" method="POST" action="{{ route('admin.portfolio.update', $project) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="admin-item-heading">
                            <div>
                                <span class="admin-form-kicker">Projekts #{{ $project->id }}</span>
                                <h3>{{ $project->title }}</h3>
                            </div>
                            <span class="admin-item-city">{{ $project->city }}</span>
                        </div>
                        <div class="admin-form-grid">
                            <label class="admin-edit-compact-field">Nosaukums<input name="title" value="{{ $project->title }}" required></label>
                            <label class="admin-edit-compact-field">Pilsēta<input name="city" value="{{ $project->city }}" required></label>
                            <label>Bruģa veids<select name="bruga_veids_id" required>
                                @foreach ($brugaVeidi as $brugaVeids)
                                    <option value="{{ $brugaVeids->id }}" @selected($project->bruga_veids_id === $brugaVeids->id)>{{ $brugaVeids->name }}</option>
                                @endforeach
                            </select></label>
                            <label>Platība m²<input name="area_m2" type="number" min="0" step="0.01" value="{{ $project->area_m2 }}"></label>
                            <label>Pabeigšanas gads<input name="completed_year" type="number" min="1900" max="2100" value="{{ $project->completed_year }}"></label>
                            <label class="admin-form-wide">Apraksts<textarea name="description" rows="3">{{ $project->description }}</textarea></label>
                            <label class="admin-form-wide">Pievienot bildes<input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple><small>Jaunas bildes tiks pievienotas esošajām.</small></label>
                        </div>
                        @if ($project->bildes->isNotEmpty())
                            <div class="admin-image-grid">
                                @foreach ($project->bildes as $bilde)
                                    <div class="admin-image-item">
                                        <img src="{{ asset('storage/' . $bilde->image_path) }}" alt="{{ $project->title }}">
                                        <button class="admin-delete" type="submit" form="delete-portfolio-image-{{ $bilde->id }}">Dzēst bildi</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="admin-item-actions">
                            <button class="admin-button" type="submit">Saglabāt izmaiņas</button>
                            <button class="admin-delete" type="submit" form="delete-portfolio-{{ $project->id }}">Dzēst</button>
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
                    <p>Portfolio vēl nav projektu.</p>
                @endforelse
            </div>
        </section>
    </main>
</x-layout>