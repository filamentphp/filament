<?php

namespace Filament\Support\Concerns;

trait CanCallHooks
{
    protected function callHook(string $hook): void
    {
        if (method_exists($this, $hook)) {
            $this->{$hook}();
        }

        /** @var array<class-string, array<string>> $traitHookSuffixes */
        static $traitHookSuffixes = [];

        $traitHookSuffixes[static::class] ??= array_unique(array_map(
            class_basename(...),
            class_uses_recursive(static::class),
        ));

        foreach ($traitHookSuffixes[static::class] as $suffix) {
            $method = $hook . $suffix;

            if (method_exists($this, $method)) {
                $this->{$method}();
            }
        }
    }
}
