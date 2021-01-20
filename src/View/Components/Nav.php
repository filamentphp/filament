<?php

namespace Filament\View\Components;

use Filament\Filament;
use Illuminate\View\Component;

class Nav extends Component
{
    public $items;

    public function __construct()
    {
        $this->items = collect(Filament::getNavigation())
            ->sortBy('sort')
            ->all();
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
