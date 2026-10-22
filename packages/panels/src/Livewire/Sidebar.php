<?php

namespace Filament\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Livewire\Concerns\HasTenantMenu;
use Filament\Livewire\Concerns\HasUserMenu;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Attributes\Exposed;
use Filament\Support\Attributes\ServerOnly;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component implements HasActions, HasSchemas
{
    use HasTenantMenu;
    use HasUserMenu;
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[Exposed]
    #[On('refresh-sidebar')]
    public function refresh(): void {}

    #[ServerOnly]
    public function render(): View
    {
        return view('filament-panels::livewire.sidebar');
    }
}
