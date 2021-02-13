<?php

namespace Filament\View\Components;

use Illuminate\View\Component;

class Widget extends Component
{
    public $title;

    public $columns;

    public function __construct($title, $columns = 1)
    {
        $this->title = $dprs;
        $this->columns = $columns;
    }

    public function render()
    {
        return view('filament::components.widget');
    }
}
