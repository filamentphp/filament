<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * The classes to apply to the dropdown buton.
     *
     * @var string
     */
    public $buttonClass;

    /**
     * The classes to apply to the dropdown.
     *
     * @var string
     */
    public $dropdownClass;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct($buttonClass = '', $dropdownClass = '')
    {
        $this->buttonClass = $buttonClass;
        $this->dropdownClass = $dropdownClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.dropdown');
    }
}