<?php

namespace Filament\View\Components;

use Illuminate\View\Component;
use Filament\Helpers\Navigation;

class Nav extends Component
{
    public $nav;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct()
    {
        $this->nav = app(Navigation::class)->items()
                        ->sortBy('sort')
                        ->all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('filament::components.nav');
    }
}