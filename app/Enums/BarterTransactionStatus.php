<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BarterTransactionStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case AwaitingCompleted = 'awaiting_completed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::AwaitingCompleted => 'Awaiting Completed',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Accepted => 'succeses',
            self::Rejected => 'danger',
            self::AwaitingCompleted => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
