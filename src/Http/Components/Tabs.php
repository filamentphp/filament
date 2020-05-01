<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Tabs extends Component
{
    /**
     * The tabs.
     *
     * @var array
     */
    public $tabs;

    /**
     * Create the component instance.
     *
     * @param  string  $tab
     * @param  array  $tabs
     * @return void
     */
    public function __construct(array $tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.tabs');
    }
}