<?php

namespace Filament\Tables\Contracts;

interface TablePersistenceStore
{
    /**
     * Determine whether a value has been stored for the key, even if that value is `null`.
     */
    public function exists(string $key): bool;

    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value): void;

    public function forget(string $key): void;
}
