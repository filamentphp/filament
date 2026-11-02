<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Livewire\Notifications as BaseComponent;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Notifications extends BaseComponent
{
    #[Unexposed]
    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }
}
