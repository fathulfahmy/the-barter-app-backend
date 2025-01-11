<?php

namespace App\Traits;

use App\Notifications\UserSuspended;
use App\Notifications\UserUnsuspended;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait Suspendable
{
    /**
     * Account is suspended permanently
     */
    protected function isSuspendedPermanently(): Attribute
    {
        return Attribute::get(
            fn () => $this->suspension_starts_at && is_null($this->suspension_ends_at)
        );
    }

    /**
     * Account is suspended temporarily
     */
    protected function isSuspendedTemporarily(): Attribute
    {
        return Attribute::get(
            fn () => $this->suspension_starts_at && $this->suspension_ends_at?->isFuture()
        );
    }

    /**
     * Suspend user account and send notification
     */
    public function suspend(string $suspension_reason, string|CarbonInterface|null $suspension_starts_at = null, string|CarbonInterface|null $suspension_ends_at = null): void
    {
        $suspension_starts_at = $suspension_starts_at instanceof CarbonInterface
            ? $suspension_starts_at
            : ($suspension_starts_at ? Carbon::parse($suspension_starts_at) : now());

        $suspension_ends_at = $suspension_ends_at instanceof CarbonInterface
            ? $suspension_ends_at
            : ($suspension_ends_at ? Carbon::parse($suspension_ends_at) : null);

        if (
            $this->suspension_starts_at == $suspension_starts_at &&
            $this->suspension_ends_at == $suspension_ends_at
        ) {
            return;
        }

        $this->update([
            'suspension_starts_at' => $suspension_starts_at,
            'suspension_ends_at' => $suspension_ends_at,
            'suspension_reason' => $suspension_reason,
        ]);

        $this->notify(new UserSuspended($this));
    }

    /**
     * Unsuspend user account and send notification
     */
    public function unsuspend(): void
    {
        if (! $this->suspension_starts_at) {
            return;
        }

        $this->update([
            'suspension_starts_at' => null,
            'suspension_ends_at' => null,
            'suspension_reason' => null,
        ]);

        $this->notify(new UserUnsuspended($this));
    }
}
