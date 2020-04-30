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
     * Create the component instance.
     *
     * @param  array  $fields
     * @return void
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
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