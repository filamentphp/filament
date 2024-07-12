<?php

namespace Filament\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Livewire\Concerns\HasTenantMenu;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component implements HasActions, HasForms
{
    use HasTenantMenu;
    use InteractsWithActions;
    use InteractsWithForms;

    #[On('refresh-sidebar')]
    public function refresh(): void {}

    public function render(): View
    {
        return view('filament-panels::livewire.sidebar');
    }
}
