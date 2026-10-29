<?php

namespace Filament\View\LegacyComponents;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/** @deprecated Use the `filament-panels::page` Blade component instead. */
class PageComponent extends Component
{
    public function render(): View
    {
        return view('filament-panels::components.page.index');
    }
}
