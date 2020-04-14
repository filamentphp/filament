<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * The unique ID of the modal.
     * 
     * @var string
     */
    public $id;

    /**
     * The label for the modal.
     * 
     * @var string
     */
    public $label;

    /**
     * Visible state of the modal.
     * 
     * @var bool
     */
    public $isOpen;

    /**
     * Allow closing modal via `esc` key.
     * 
     * @var bool
     */
    public $escClose;

    /**
     * Allow clicking outside of modal content to close.
     * 
     * @var bool
     */
    public $clickOutside;

    /**
     * Create the component instance.
     *
     * @param  bool  $isOpen
     * @return void
     */
    public function __construct($id, $label, $isOpen = false, $escClose = false, $clickOutside = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->isOpen = $isOpen;
        $this->escClose = $escClose;
        $this->clickOutside = $clickOutside;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.modal');
    }
}