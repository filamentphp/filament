<?php

namespace Filament\Tables\State;

use Filament\Tables\Contracts\TableStateStore;

class SessionTableStateStore implements TableStateStore
{
    public function has(string $key): bool
    {
        return session()->exists($key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return session()->get($key, $default);
    }

    public function put(string $key, mixed $value): void
    {
        session()->put($key, $value);
    }

    public function forget(string $key): void
    {
        session()->forget($key);
    }
}
