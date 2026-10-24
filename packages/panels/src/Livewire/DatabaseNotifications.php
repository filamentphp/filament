<?php

namespace Filament\Livewire;

use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Facades\Filament;
use Filament\Notifications\Livewire\DatabaseNotifications as BaseComponent;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class DatabaseNotifications extends BaseComponent
{
    #[Locked]
    public ?DatabaseNotificationsPosition $position = null;

    #[Unexposed]
    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }

    #[Unexposed]
    public function getPollingInterval(): ?string
    {
        return Filament::getDatabaseNotificationsPollingInterval();
    }

    #[Unexposed]
    public function getTrigger(): ?View
    {
        return (($this->position ?? filament()->getDatabaseNotificationsPosition()) === DatabaseNotificationsPosition::Topbar)
            ? view('filament-panels::components.topbar.database-notifications-trigger')
            : view('filament-panels::components.sidebar.database-notifications-trigger');
    }
}
