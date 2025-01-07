<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BarterServiceStatus: string implements HasColor, HasLabel
{
    case Enabled = 'enabled';
    case Disabled = 'disabled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Enabled => 'Enabled',
            self::Disabled => 'Disabled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Enabled => 'primary',
            self::Disabled => 'gray',
        };
    }
}
