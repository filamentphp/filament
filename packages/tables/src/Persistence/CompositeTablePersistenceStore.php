<?php

namespace Filament\Tables\Persistence;

use Filament\Tables\Contracts\TablePersistenceStore;

class CompositeTablePersistenceStore implements TablePersistenceStore
{
    /**
     * @param  array<TablePersistenceStore>  $stores
     */
    public function __construct(protected array $stores) {}

    public function exists(string $key): bool
    {
        foreach ($this->stores as $store) {
            if ($store->exists($key)) {
                return true;
            }
        }

        return false;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        foreach ($this->stores as $store) {
            if ($store->exists($key)) {
                return $store->get($key);
            }
        }

        return $default;
    }

    public function put(string $key, mixed $value): void
    {
        foreach ($this->stores as $store) {
            $store->put($key, $value);
        }
    }

    public function forget(string $key): void
    {
        foreach ($this->stores as $store) {
            $store->forget($key);
        }
    }
}
