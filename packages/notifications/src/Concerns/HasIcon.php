<?php

namespace Filament\Notifications\Concerns;

use BackedEnum;
use Filament\Notifications\View\NotificationsIconAlias;
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
            'danger' => FilamentIcon::resolve(NotificationsIconAlias::NOTIFICATION_DANGER) ?? Heroicon::OutlinedXCircle,
            'info' => FilamentIcon::resolve(NotificationsIconAlias::NOTIFICATION_INFO) ?? Heroicon::OutlinedInformationCircle,
            'success' => FilamentIcon::resolve(NotificationsIconAlias::NOTIFICATION_SUCCESS) ?? Heroicon::OutlinedCheckCircle,
            'warning' => FilamentIcon::resolve(NotificationsIconAlias::NOTIFICATION_WARNING) ?? Heroicon::OutlinedExclamationCircle,
            default => null,
        };
    }
}
