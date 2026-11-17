<?php

namespace Filament\Tables\Persistence;

use Filament\Tables\Contracts\TablePersistenceStore;

class SessionTablePersistenceStore implements TablePersistenceStore
{
    public function exists(string $key): bool
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
