<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Fields extends Component
{
    /**
     * Array of fields.
     *
     * @var array
     */
    public $fields;

    /**
     * Optional assigned field group.
     *
     * @var string|null
     */
    public $group;

    /**
     * Create the component instance.
     *
     * @param  array  $fields
     * @return void
     */
    public function __construct($fields, $group = null)
    {
        $this->fields = $fields;
        $this->group = $group;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.fields');
    }
}