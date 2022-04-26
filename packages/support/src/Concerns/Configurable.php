<?php

namespace Filament\Support\Concerns;

trait Configurable
{
    protected static array $configurations = [];

    public static function configureUsing(callable $callback): void
    {
        static::$configurations[] = $callback;
    }

    public function configureObject(): static
    {
        foreach (static::$configurations as $callback) {
            $callback($this);
        }

        return $this;
    }
}
