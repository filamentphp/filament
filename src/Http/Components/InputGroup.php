<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class InputGroup extends Component
{
    /**
     * The input group id.
     *
     * @var string
     */
    public $id;

    /**
     * The input group label.
     *
     * @var string
     */
    public $label;

    /**
     * Determins if input group is required.
     *
     * @var boolean
     */
    public $required;

    /**
     * The input group hint.
     *
     * @var string
     */
    public $hint;

    /**
     * The input group info.
     *
     * @var string
     */
    public $info;

    /**
     * Create the component instance.
     *
     * @param  string  $id
     * @param  string  $label
     * @param  boolean  $required
     * @return void
     */
    public function __construct($id = '', $label = '', $required = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->required = (bool) $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.input-group');
    }
}