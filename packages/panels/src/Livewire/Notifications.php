<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Livewire\Notifications as BaseComponent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Notifications extends BaseComponent
{
    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }
}
