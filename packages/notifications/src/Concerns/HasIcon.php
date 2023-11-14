<?php

namespace Filament\Notifications\Concerns;

use Filament\Support\Concerns\HasIcon as BaseTrait;
use Filament\Support\Facades\FilamentIcon;

trait HasIcon
{
    use BaseTrait {
        getIcon as baseGetIcon;
    }

    public function getIcon(): ?string
    {
        return $this->baseGetIcon() ?? match ($this->getStatus()) {
            'danger' => FilamentIcon::resolve('notifications::notification.danger') ?? 'heroicon-o-x-circle',
            'info' => FilamentIcon::resolve('notifications::notification.info') ?? 'heroicon-o-information-circle',
            'success' => FilamentIcon::resolve('notifications::notification.success') ?? 'heroicon-o-check-circle',
            'warning' => FilamentIcon::resolve('notifications::notification.warning') ?? 'heroicon-o-exclamation-circle',
            default => null,
        };
    }
}
