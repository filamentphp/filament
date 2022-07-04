<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
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
