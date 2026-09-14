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
    </main>
</x-layout>
