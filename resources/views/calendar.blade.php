<x-layout title="{{ __('Reservation calendar') }} | Abrugis">
    <main class="client-calendar-page">
        <div class="client-calendar-intro">
            <p class="eyebrow">{{ __('Planning') }}</p>
            <h1>{{ __('Reservation calendar') }}</h1>
            <p>{{ __('See which dates are already reserved before choosing a preferred work date.') }}</p>
            <p class="client-calendar-note"><i class="calendar-dot"></i>{{ __('Occupied date') }}</p>
        </div>

        <div class="admin-calendar">
            <div class="calendar-toolbar">
                <a class="calendar-nav" href="{{ route('calendar', ['calendar_month' => $calendarMonth->copy()->subMonth()->format('Y-m')]) }}" aria-label="{{ __('Previous month') }}">&larr;</a>
                <h3>{{ $calendarMonth->translatedFormat('F Y') }}</h3>
                <a class="calendar-nav" href="{{ route('calendar', ['calendar_month' => $calendarMonth->copy()->addMonth()->format('Y-m')]) }}" aria-label="{{ __('Next month') }}">&rarr;</a>
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
                        $isOccupied = $bookedDates->contains($dateKey);
                    @endphp
                    <div class="calendar-day {{ $isOccupied ? 'calendar-day-occupied' : '' }}">
                        <strong>{{ $day }}</strong>
                        @if ($isOccupied)
                            <span class="calendar-occupied-label">{{ __('Occupied') }}</span>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </main>
</x-layout>
