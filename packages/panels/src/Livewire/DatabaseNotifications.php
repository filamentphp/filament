<?php

namespace Filament\Livewire;

use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Facades\Filament;
use Filament\Notifications\Livewire\DatabaseNotifications as BaseComponent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class DatabaseNotifications extends BaseComponent
{
    #[Locked]
    public ?DatabaseNotificationsPosition $position = null;

    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }

    public function getPollingInterval(): ?string
    {
        return Filament::getDatabaseNotificationsPollingInterval();
    }

    public function getTrigger(): ?View
    {
        return (($this->position ?? filament()->getDatabaseNotificationsPosition()) === DatabaseNotificationsPosition::Topbar)
            ? view('filament-panels::components.topbar.database-notifications-trigger')
            : view('filament-panels::components.sidebar.database-notifications-trigger');
    }
}
