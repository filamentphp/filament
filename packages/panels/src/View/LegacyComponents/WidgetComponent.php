<?php

namespace Filament\View\LegacyComponents;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/** @deprecated Use the `filament-widgets::widget` Blade component instead. */
class WidgetComponent extends Component
{
    public function render(): View
    {
        return view('filament-widgets::components.widget');
    }
}
