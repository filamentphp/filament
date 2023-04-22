<?php

namespace Filament\Http\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Http\Livewire\DatabaseNotifications as BaseComponent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class DatabaseNotifications extends BaseComponent
{
    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }

    public function getPollingInterval(): ?string
    {
        return Filament::getDatabaseNotificationsPollingInterval();
    }

    public function getTrigger(): View
    {
        return view('filament::components.layouts.app.topbar.database-notifications-trigger');
    }

    public static function getName(): string
    {
        return 'filament.core.database-notifications';
    }
}
