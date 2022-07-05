<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
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
            /**
             * @var string $filter
             * @phpstan-ignore-next-line
             */
            $filter = $this->parseFilterName($filter);

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
            /**
             * @var string $name
             * @phpstan-ignore-next-line
             */
            $name = $this->parseFilterName($name);

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

    public function parseFilterName(): Closure
    {
        return function (string $name): string {
            if (! class_exists($name)) {
                return $name;
            }

            if (! is_subclass_of($name, BaseFilter::class)) {
                return $name;
            }

            return $name::getDefaultName();
        };
    }
}
