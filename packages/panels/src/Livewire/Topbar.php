<?php

namespace Filament\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;

class Topbar extends Component
{
    #[On('refresh-topbar')]
    public function refresh(): void {}

    public function render(): View
    {
        return view('filament-panels::livewire.topbar');
    }
}
