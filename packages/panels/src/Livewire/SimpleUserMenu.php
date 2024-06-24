<?php

namespace Filament\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Livewire\Concerns\HasUserMenu;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SimpleUserMenu extends Component implements HasActions, HasForms
{
    use HasUserMenu;
    use InteractsWithActions;
    use InteractsWithForms;

    public function render(): View
    {
        return view('filament-panels::livewire.simple-user-menu');
    }
}
