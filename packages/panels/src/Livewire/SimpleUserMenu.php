<?php

namespace Filament\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Livewire\Concerns\HasUserMenu;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SimpleUserMenu extends Component implements HasActions, HasSchemas
{
    use HasUserMenu;
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function render(): View
    {
        return view('filament-panels::livewire.simple-user-menu');
    }
}
