<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Tab extends Component
{
    /**
     * The tab id.
     *
     * @var string
     */
    public $id;

    /**
     * Create the component instance.
     *
     * @param  string  $id
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return <<<'blade'
            <div tabindex="0" role="tabpanel" id="{{ $id }}-tab" aria-labelledby="{{ $id }}" x-show="tab === '{{ $id }}'" :hidden="tab !== '{{ $id }}'">
                {{ $slot }}
            </div>
        blade;
    }
}