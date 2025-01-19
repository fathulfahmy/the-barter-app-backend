<?php

namespace App\Traits;

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
            fn () => $this->suspension_starts_at && $this->suspension_ends_at
        );
    }

    public function scopeIsSuspendedPermanently(): void
    {
        $this->is_suspended_permanently;
    }

    public function scopeIsSuspendedTemporarily(): void
    {
        $this->is_suspended_temporarily;
    }

    /**
     * Suspend user account and send notification
     */
    public function suspend(int $suspension_reason_id, string|CarbonInterface|null $suspension_starts_at = null, string|CarbonInterface|null $suspension_ends_at = null): void
    {
        $this->update([
            'suspension_starts_at' => $suspension_starts_at ?? now(),
            'suspension_ends_at' => $suspension_ends_at,
            'suspension_reason_id' => $suspension_reason_id,
        ]);
    }

    /**
     * Unsuspend user account and send notification
     */
    public function unsuspend(): void
    {
        $this->update([
            'suspension_starts_at' => null,
            'suspension_ends_at' => null,
            'suspension_reason_id' => null,
        ]);
    }
}
