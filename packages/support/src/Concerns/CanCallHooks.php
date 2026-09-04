<?php

namespace Filament\Support\Concerns;

trait CanCallHooks
{
    protected function callHook(string $hook): void
    {
        if (method_exists($this, $hook)) {
            $this->{$hook}();
        }

        $calledTraitHooks = [];

        foreach (class_uses_recursive($this) as $trait) {
            $method = $hook . class_basename($trait);

            if (method_exists($this, $method) && (! in_array($method, $calledTraitHooks, strict: true))) {
                $this->{$method}();

                $calledTraitHooks[] = $method;
            }
        }
    }
}
