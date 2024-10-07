<?php

namespace Filament\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Livewire\Concerns\HasTenantMenu;
use Filament\Livewire\Concerns\HasUserMenu;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Topbar extends Component implements HasActions, HasForms
{
    use HasTenantMenu;
    use HasUserMenu;
    use InteractsWithActions;
    use InteractsWithForms;

    #[On('refresh-topbar')]
    public function refresh(): void {}

    public function render(): View
    {
        return view('filament-panels::livewire.topbar');
    }
}
