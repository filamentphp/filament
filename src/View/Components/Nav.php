<?php

namespace Filament\View\Components;

use Filament\Models\NavigationItem;
use Illuminate\View\Component;

class Nav extends Component
{
    public $items;

    public function __construct()
    {
        $this->items = NavigationItem::all()->sortBy('label')->sortBy('sort');
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
