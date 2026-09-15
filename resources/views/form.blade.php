<x-layout title="Pieteikums | Abrugis">
    <main class="form-page">
        <section class="form-shell">
            <div class="form-intro">
                <p class="eyebrow">Pieteikums</p>
                <h1>Izveidot pieteikumu</h1>
                <p>Aizpildi formu, un mēs ar tevi sazināsimies par projekta iespējām.</p>
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
                    <label for="client_name">Vārds
                        <input id="client_name" name="client_name" type="text" value="{{ old('client_name', auth()->user()->name) }}" required autofocus>
                    </label>

                    <label for="client_email">E-pasts
                        <input id="client_email" name="client_email" type="email" value="{{ old('client_email', auth()->user()->email) }}" required>
                    </label>

                    <label for="client_phone">Telefona numurs
                        <input id="client_phone" name="client_phone" type="tel" value="{{ old('client_phone') }}" required>
                    </label>

                    <label for="bruga_veids_id">Vēlamais bruģa veids
                        <select id="bruga_veids_id" name="bruga_veids_id">
                            <option value="">Izvēlies bruģa veidu</option>
                            @foreach ($brugaVeidi as $brugaVeids)
                                <option value="{{ $brugaVeids->id }}" @selected(old('bruga_veids_id') == $brugaVeids->id)>
                                    {{ $brugaVeids->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label for="area_m2">Aptuvenā platība m²
                        <input id="area_m2" name="area_m2" type="number" min="1" step="0.01" value="{{ old('area_m2') }}">
                    </label>
                </div>

                <label for="project_description">Projekta apraksts
                    <textarea id="project_description" name="project_description" rows="6" required>{{ old('project_description') }}</textarea>
                </label>

                <button type="submit">Nosūtīt pieteikumu</button>
            </form>

            @if (session('success'))
                <p class="success-note" role="status">{{ session('success') }}</p>
            @endif
        </section>

        <section class="form-shell applications-shell">
            <div class="form-intro">
                <p class="eyebrow">Klienta zona</p>
                <h2>Mani pieteikumi</h2>
                <p>Šeit vari redzēt savu pieteikumu statusu un iesniegto informāciju.</p>
            </div>

            @if ($pieteikumi->isEmpty())
                <p class="empty-state">Te vēl nav nosūtītu pieteikumu.</p>
            @else
                <div class="application-list">
                    @php
                        $statusLabels = [
                            'new' => 'Jauns',
                            'contacted' => 'Sazināsimies',
                            'approved' => 'Apstiprināts',
                            'completed' => 'Pabeigts',
                            'rejected' => 'Noraidīts',
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
                                <span>{{ $pieteikums->pavingType?->name ?? 'Bruģa veids nav norādīts' }}</span>
                                <span>{{ $pieteikums->area_m2 ? $pieteikums->area_m2 . ' m²' : 'Platība nav norādīta' }}</span>
                            </div>
                            @if ($pieteikums->review)
                                <p class="application-review-note">Atsauksme iesniegta · {{ $pieteikums->review->rating }}/5 zvaigznes</p>
                            @elseif ($pieteikums->status === 'completed')
                                <p class="application-review-note application-review-pending">Par šo pieteikumu vari atstāt atsauksmi zemāk.</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($pieteikumi->contains(fn ($pieteikums) => $pieteikums->status === 'completed' && ! $pieteikums->review))
            <section class="form-shell review-shell">
                <div class="form-intro">
                    <p class="eyebrow">Tava pieredze</p>
                    <h2>Atstāt atsauksmi</h2>
                    <p>Dalies ar pieredzi par pabeigtu projektu.</p>
                </div>
                <form class="application-form" method="POST" action="{{ route('atsauksmes.store') }}">
                    @csrf
                    <label for="pieteikums_id">Pabeigtais projekts
                        <select id="pieteikums_id" name="pieteikums_id" required>
                            @foreach ($pieteikumi as $pieteikums)
                                @if ($pieteikums->status === 'completed' && ! $pieteikums->review)
                                    <option value="{{ $pieteikums->id }}">{{ $pieteikums->project_description }}</option>
                                @endif
                            @endforeach
                        </select>
                    </label>
                    <label for="rating">Vērtējums
                        <select id="rating" name="rating" required>
                            <option value="5">5 zvaigznes</option>
                            <option value="4">4 zvaigznes</option>
                            <option value="3">3 zvaigznes</option>
                            <option value="2">2 zvaigznes</option>
                            <option value="1">1 zvaigzne</option>
                        </select>
                    </label>
                    <label for="atsauksme">Atsauksme
                        <textarea id="atsauksme" name="atsauksme" rows="4" maxlength="2000" placeholder="Kā jums patika sadarbība?"></textarea>
                    </label>
                    <button type="submit">Nosūtīt atsauksmi</button>
                </form>
            </section>
        @endif
    </main>
</x-layout>
