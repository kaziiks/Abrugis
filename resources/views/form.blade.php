<x-layout title="Pieteikums | Abrugis">
    <main>
        <h1>Izveidot pieteikumu</h1>
        <p>Aizpildi formu, un mēs ar tevi sazināsimies par projekta iespējām.</p>

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('form.store') }}">
            @csrf

            <label for="client_name">Vārds</label>
            <input id="client_name" name="client_name" type="text" value="{{ old('client_name', auth()->user()->name) }}" required autofocus>
            <br>
            <label for="client_email">E-pasts</label>
            <input id="client_email" name="client_email" type="email" value="{{ old('client_email', auth()->user()->email) }}" required>
            <br>
            <label for="client_phone">Telefona numurs</label>
            <input id="client_phone" name="client_phone" type="tel" value="{{ old('client_phone') }}" required>
            <br>
            <label for="bruga_veids_id">Vēlamais bruģa veids</label>
            <select id="bruga_veids_id" name="bruga_veids_id">
                <option value="">Izvēlies bruģa veidu</option>
                @foreach ($brugaVeidi as $brugaVeids)
                    <option value="{{ $brugaVeids->id }}" @selected(old('bruga_veids_id') == $brugaVeids->id)>
                        {{ $brugaVeids->name }}
                    </option>
                @endforeach
            </select>
            <br>
            <label for="area_m2">Aptuvenā platība m²</label>
            <input id="area_m2" name="area_m2" type="number" min="1" step="0.01" value="{{ old('area_m2') }}">
            <br>
            <label for="project_description">Projekta apraksts</label>
            <textarea id="project_description" name="project_description" rows="6" required>{{ old('project_description') }}</textarea>

            <button type="submit">Nosūtīt pieteikumu</button>
        </form>
        @if (session('success'))
            <p role="status">{{ session('success') }}</p>
        @endif
    </main>
</x-layout>
