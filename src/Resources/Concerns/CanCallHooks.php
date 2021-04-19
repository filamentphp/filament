<?php

namespace Filament\Resources\Concerns;

trait CanCallHooks
{
    protected function callHook($hook)
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }
}
