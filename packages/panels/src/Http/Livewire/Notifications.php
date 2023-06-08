<?php

namespace Filament\Http\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Http\Livewire\Notifications as BaseComponent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Notifications extends BaseComponent
{
    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }

    public static function getName(): string
    {
        return 'filament.core.notifications';
    }
}
