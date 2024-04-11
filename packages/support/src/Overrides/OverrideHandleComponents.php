<?php

namespace Filament\Support\Overrides;

use Filament\Support\Partials\SupportPartials;
use Livewire\Mechanisms\HandleComponents\HandleComponents;

class OverrideHandleComponents extends HandleComponents
{
    protected function render($component, $default = null)
    {
        $hook = app(SupportPartials::class);
        $hook->setComponent($component);

        if ($hook->shouldSkipRender()) {
            $component->skipRender();
        }

        return parent::render($component, $default);
    }
}
