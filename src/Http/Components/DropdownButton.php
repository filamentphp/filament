<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class DropdownButton extends Component
{
    /**
     * The button type.
     *
     * @var string
     */
    public $type;

    /**
     * The button label.
     *
     * @var string
     */
    public $label;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct($type = 'button', $label)
    {
        $this->type = $type;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.dropdown-button');
    }
}