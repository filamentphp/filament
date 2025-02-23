<?php

namespace Filament\Notifications\Concerns;

use BackedEnum;
use Filament\Support\Concerns\HasIcon as BaseTrait;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

trait HasIcon
{
    use BaseTrait {
        getIcon as getBaseIcon;
    }

    public function getIcon(): string | BackedEnum | null
    {
        return $this->getBaseIcon() ?? match ($this->getStatus()) {
            'danger' => FilamentIcon::resolve('notifications::notification.danger') ?? Heroicon::OutlinedXCircle,
            'info' => FilamentIcon::resolve('notifications::notification.info') ?? Heroicon::OutlinedInformationCircle,
            'success' => FilamentIcon::resolve('notifications::notification.success') ?? Heroicon::OutlinedCheckCircle,
            'warning' => FilamentIcon::resolve('notifications::notification.warning') ?? Heroicon::OutlinedExclamationCircle,
            default => null,
        };
    }
}
