<?php

namespace Filament\Tables\Concerns;

trait Configurable
{
    protected static array $configurables = [];

    public static function configure(callable $callback)
    {
        static::$configurables[] = $callback;
    }

    protected function configureObject()
    {
        foreach (static::$configurables as $callback) {
            $callback($this);
        }
    }
}
