<?php

namespace Alpine\Http\Components;

use Illuminate\View\Component;

class Input extends Component
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
     * The input value.
     *
     * @var string
     */
    public $value;

    /**
     * The input classes.
     *
     * @var string
     */
    public $class;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $name
     * @return void
     */
    public function __construct($type = 'text', $name, $value = '', $class = '')
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = old($name, $value);
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('alpine::components.input');
    }
}