<?php

namespace Filament\Resources\RelationManager\Concerns;

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
