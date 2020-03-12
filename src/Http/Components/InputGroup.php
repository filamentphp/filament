<?php

namespace Alpine\Http\Components;

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
     * The input group hint.
     *
     * @var string
     */
    public $hint;

    /**
     * Determins if input group is required.
     *
     * @var boolean
     */
    public $required;

    /**
     * Create the component instance.
     *
     * @param  string  $id
     * @param  string  $label
     * @param  boolean  $required
     * @return void
     */
    public function __construct($id = '', $label = '', $hint = '', $required = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->hint = $hint;
        $this->required = (bool) $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('alpine::components.input-group');
    }
}