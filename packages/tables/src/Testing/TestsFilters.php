<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
        return function (string $name, $data = null): static {
            /**
             * @var string $name
             * @phpstan-ignore-next-line
             */
            $name = $this->parseFilterName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableFilterExists($name);

            $filter = $this->instance()->getCachedTableFilter($name);

            if ($filter instanceof TernaryFilter) {
                if ($data === true || ($data === null && func_num_args() === 1)) {
                    $data = ['value' => true];
                } else {
                    $data = ['value' => $data];
                }
            } elseif ($filter instanceof MultiSelectFilter) {
                $data = ['values' => array_map(
                    fn ($record) => $record instanceof Model ? $record->getKey() : $record,
                    Arr::wrap($data ?? []),
                )];
            } elseif ($filter instanceof SelectFilter) {
                $data = ['value' => $data instanceof Model ? $data->getKey() : $data];
            } elseif (! is_array($data)) {
                $data = ['isActive' => $data === true || $data === null];
            }

            $this->set("tableFilters.{$filter->getName()}", $data);

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

    public function removeTableFilter(): Closure
    {
        return function (string $filter, ?string $field = null): static {
            $this->call('removeTableFilter', $filter, $field);

            return $this;
        };
    }

    public function removeTableFilters(): Closure
    {
        return function (): static {
            $this->call('removeTableFilters');

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

            $filter = $livewire->getCachedTableFilter($name);

            Assert::assertInstanceOf(
                BaseFilter::class,
                $filter,
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
