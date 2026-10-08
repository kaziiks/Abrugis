<?php

namespace App\Services;

use App\Models\Application;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ApplicationReservationService
{
    public function create(array $attributes): Application
    {
        $requestedDate = $attributes['requested_date'] ?? null;

        return $this->withDateLock($requestedDate, function () use ($attributes, $requestedDate): Application {
            $this->ensureDateIsAvailable($requestedDate);

            return Application::create($attributes);
        });
    }

    public function updateStatus(Application $application, string $status): void
    {
        $requestedDate = $application->requested_date?->format('Y-m-d');
        $reservesDate = in_array($status, ['approved', 'completed'], true);

        $this->withDateLock($reservesDate ? $requestedDate : null, function () use ($application, $requestedDate, $status, $reservesDate): void {
            if ($reservesDate) {
                $this->ensureDateIsAvailable($requestedDate, $application->id);
            }

            $application->update(['status' => $status]);
        });
    }

    private function withDateLock(?string $requestedDate, Closure $callback): mixed
    {
        if (! $requestedDate) {
            return $callback();
        }

        try {
            return Cache::lock('abrugis-reservation-'.$requestedDate, 20)->block(10, $callback);
        } catch (LockTimeoutException) {
            throw ValidationException::withMessages([
                'requested_date' => 'Konsultācijas datumu nevarēja pārbaudīt. Lūdzu, mēģiniet vēlreiz.',
            ]);
        }
    }

    private function ensureDateIsAvailable(?string $requestedDate, ?int $exceptApplicationId = null): void
    {
        if (! $requestedDate) {
            return;
        }

        $dateIsReserved = Application::query()
            ->where('requested_date', $requestedDate)
            ->whereIn('status', ['approved', 'completed'])
            ->when($exceptApplicationId, fn ($query) => $query->where('id', '!=', $exceptApplicationId))
            ->exists();

        if ($dateIsReserved) {
            throw ValidationException::withMessages([
                'requested_date' => 'Šis konsultācijas datums jau ir apstiprināts. Lūdzu, izvēlieties citu datumu.',
            ]);
        }
    }
}
