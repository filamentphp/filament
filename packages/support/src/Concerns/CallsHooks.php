<?php

namespace Filament\Support\Concerns;

trait CallsHooks
{
    /**
     * Calls the hook method with the given name, if it exists on the class,
     * followed by any trait-named hook methods (`{$hook}{TraitName}()`) defined
     * by the traits used by the class, similar to Eloquent model trait `boot`
     * methods.
     */
    protected function callHook(string $hook): void
    {
        if (method_exists($this, $hook)) {
            $this->{$hook}();
        }

        $calledTraitHooks = [];

        foreach (class_uses_recursive($this) as $trait) {
            $method = $hook . class_basename($trait);

            if (method_exists($this, $method) && (! in_array($method, $calledTraitHooks))) {
                $this->{$method}();

                $calledTraitHooks[] = $method;
            }
        }
    }
}
