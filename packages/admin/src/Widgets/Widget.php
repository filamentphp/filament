<?php

namespace Filament\Widgets;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Widget extends Component
{
    protected static string $view;

    protected function getViewData(): array
    {
        return [];
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }
}
