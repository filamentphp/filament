<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
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
        return function (string $filter, ?array $data = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableFilterExists($filter);

            $data ??= ['isActive' => true];

            $this->set("tableFilters.{$filter}", $data);

            return $this;
        };
    }

    public function resetTableFilters(): Closure
    {
        return function (): static {
            $this->call('resetTableFiltersForm');

            return $this;
        };
    }

    public function assertTableFilterExists(): Closure
    {
        return function (string $name): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableFilter($name);

            Assert::assertInstanceOf(
                Filter::class,
                $action,
                message: "Failed asserting that a table filter with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }
}
