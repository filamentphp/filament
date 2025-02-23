<?php

namespace Filament\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Livewire\Concerns\HasTenantMenu;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component implements HasActions, HasSchemas
{
    use HasTenantMenu;
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[On('refresh-sidebar')]
    public function refresh(): void {}

    public function render(): View
    {
        return view('filament-panels::livewire.sidebar');
    }
}
