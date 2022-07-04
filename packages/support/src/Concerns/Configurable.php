<?php

namespace Filament\Support\Concerns;

use Closure;

trait Configurable
{
    protected static array $configurations = [];

    protected static array $importantConfigurations = [];

    public static function configureUsing(Closure $callback, ?Closure $during = null, bool $isImportant = false)
    {
        if ($isImportant) {
            static::$importantConfigurations[static::class] ??= [];
            static::$importantConfigurations[static::class][] = $callback;
        } else {
            static::$configurations[static::class] ??= [];
            static::$configurations[static::class][] = $callback;
        }

        if (! $during) {
            return;
        }

        try {
            return $during();
        } finally {
            if ($isImportant) {
                array_pop(static::$importantConfigurations[static::class]);
            } else {
                array_pop(static::$configurations[static::class]);
            }
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

        $this->setUp();

        foreach (static::$importantConfigurations as $classToConfigure => $configurationCallbacks) {
            if (! $this instanceof $classToConfigure) {
                continue;
            }

            foreach ($configurationCallbacks as $configure) {
                $configure($this);
            }
        }

        return $this;
    }

    protected function setUp(): void
    {
    }
}
