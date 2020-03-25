<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Form extends Component
{
    /**
     * The form type.
     *
     * @var string
     */
    public $method;

    /**
     * The form HTTP verb.
     *
     * @var string
     */
    public $httpVerb;

    /**
     * The form action.
     *
     * @var string
     */
    public $action;

    /**
     * Create the component instance.
     *
     * @param  string  $method
     * @param  string  $action
     * @return void
     */
    public function __construct($method = 'POST', $httpVerb = null, $action)
    {
        $this->method = $method;
        $this->httpVerb = $httpVerb ?? $this->method;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return <<<'blade'
            <form method="{{ $method }}" action="{{ $action }}" {{ $attributes }}>
                @csrf
                @method($httpVerb)
                {{ $slot }}
            </form>
        blade;
    }
}