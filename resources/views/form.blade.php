<x-layout title="{{ __('Application') }} | Abrugis">
    <main class="form-page">
        <section class="form-shell">
            <div class="form-intro">
                <p class="eyebrow">{{ __('Application') }}</p>
                <h1>{{ __('Create an application') }}</h1>
                <p>{{ __('Fill in the form and we will contact you about your project options.') }}</p>
            </div>

            @if ($estimate)
                <aside class="estimate-card application-estimate">
                    <p class="eyebrow">{{ __('Your calculator estimate') }}</p>
                    <div class="estimate-total">{{ number_format($estimate['total'], 2, ',', ' ') }} <small>€</small></div>
                    <p>{{ $estimate['paving_name'] }} · {{ number_format($estimate['area'], 2, ',', ' ') }} m² · {{ __('Base preparation') }}: {{ __($estimate['base'] === 'standard' ? 'Standard' : 'Reinforced') }}</p>
                    <p>{{ __('excluding curbs and additional landscaping') }}</p>
                </aside>
            @endif

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
                        <input id="client_email" name="client_email" type="email" value="{{ auth()->user()->email }}" readonly required>
                    </label>

                    <label for="client_phone">{{ __('Phone number') }}
                        <input id="client_phone" name="client_phone" type="tel" value="{{ old('client_phone') }}" required>
                    </label>

                    <label for="paving_type_id">{{ __('Preferred paving type') }}
                        <select id="paving_type_id" name="paving_type_id" @disabled($estimate)>
                            <option value="">{{ __('Choose a paving type') }}</option>
                            @foreach ($pavingTypes as $pavingType)
                                <option value="{{ $pavingType->id }}" @selected(old('paving_type_id', $estimate['paving_type_id'] ?? null) == $pavingType->id)>
                                    {{ $pavingType->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label for="area_m2">{{ __('Approximate area m²') }}
                        <input id="area_m2" name="area_m2" type="number" min="1" step="0.01" value="{{ old('area_m2', $estimate['area'] ?? '') }}" @readonly($estimate)>
                    </label>

                    <label for="requested_date">{{ __('Preferred consultation date') }}
                        <input id="requested_date" name="requested_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('requested_date') }}">
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

    </main>
</x-layout>
