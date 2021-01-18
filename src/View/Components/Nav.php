<?php

namespace Filament\View\Components;

use Illuminate\View\Component;

class Nav extends Component
{
    public $nav;

    public function __construct()
    {
        $nav = app('Filament\Navigation');

        $this->nav = $nav->items()
            ->sortBy('sort')
            ->all();
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
