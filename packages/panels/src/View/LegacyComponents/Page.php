<?php

namespace Filament\View\LegacyComponents;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Page extends Component
{
    public function render(): View
    {
        return view('filament-panels::components.page.index');
    }
}
