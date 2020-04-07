<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Table extends Component
{
    /**
     * The table headers.
     *
     * @var string
     */
    public $headers;

    /**
     * The table rows.
     *
     * @var string
     */
    public $rows;

    /**
     * Create the component instance.
     *
     * @param  array  $headers
     * @param  array  $rows
     * @return void
     */
    public function __construct(array $headers, array $rows)
    {
        $this->headers = $headers;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.table');
    }
}