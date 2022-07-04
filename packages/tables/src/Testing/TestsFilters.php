<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasTable instance()
 *
 * @mixin TestableLivewire
 */
class TestsFilters
{
    public function filterTable(): Closure
    {
        return function (string $filter, array $data = ['value' => true]): static {
            $this->instance()->tableFilters[$filter] = $data;

            return $this;
        };
    }

    public function resetTableFilters(): Closure
    {
        return function (): static {
            $this->instance()->resetTableFiltersForm();

            return $this;
        };
    }
}
