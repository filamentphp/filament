<?php

namespace Filament\Support\Concerns;

use Closure;

trait Configurable
{
    protected static array $configurations = [];

    public static function configureUsing(Closure $callback, ?Closure $during = null)
    {
        static::$configurations[static::class] ??= [];
        static::$configurations[static::class][] = $callback;

        if (! $during) {
            return;
        }

        try {
            return $during();
        } finally {
            array_pop(static::$configurations[static::class]);
        }
    }

    public function configure(): static
    {
        foreach (static::$configurations as $classToConfigure => $configurationCallbacks) {
            if (! $this instanceof $classToConfigure) {
                continue;
            }

            foreach ($configurationCallbacks as $configure) {
                $configure($this);
            }
        }

        return $this;
    }
}
