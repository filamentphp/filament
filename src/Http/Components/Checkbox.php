<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Checkbox extends Component
{
    /**
     * The input type.
     *
     * @var string
     */
    public $type;

    /**
     * The input name.
     *
     * @var string
     */
    public $name;

    /**
     * The input label.
     *
     * @var string
     */
    public $label;

    /**
     * The input status.
     *
     * @var boolean
     */
    public $checked;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $name
     * @return void
     */
    public function __construct($type = 'checkbox', $name, $label, $checked = false)
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->checked = (bool) old($name, $checked);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.checkbox');
    }
}