<?php

namespace Filament\Tables\Testing;

use BackedEnum;
use Closure;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasTable instance()
 *
 * @mixin Testable
 */
class TestsFilters
{
    public function filterTable(): Closure
    {
        return function (string $name, $data = null): static {
            $name = $this->instance()->parseTableFilterName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableFilterExists($name);

            $filter = $this->instance()->getTable()->getFilter($name);

            if ($filter instanceof TernaryFilter) {
                if ($data === true || ($data === null && func_num_args() === 1)) {
                    $data = ['value' => true];
                } else {
                    $data = ['value' => $data];
                }
            } elseif ($filter instanceof SelectFilter) {
                if ($filter->isMultiple()) {
                    $data = ['values' => array_map(
                        fn ($data) => $data instanceof Model ? $data->getKey() : ($data instanceof BackedEnum ? $data->value : $data),
                        Arr::wrap($data ?? []),
                    )];
                } else {
                    $data = ['value' => $data instanceof Model ? $data->getKey() : ($data instanceof BackedEnum ? $data->value : $data)];
                }
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
            $this->call('removeTableFilter', $this->instance()->parseTableFilterName($filter), $field);

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
        return function (string $name, ?Closure $checkFilterUsing = null): static {
            $name = $this->instance()->parseTableFilterName($name);

            $filter = $this->instance()->getTable()->getFilter($name);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                BaseFilter::class,
                $filter,
                "Failed asserting that a table filter with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            if ($checkFilterUsing) {
                Assert::assertTrue(
                    $checkFilterUsing($filter),
                    "Failed asserting that a table filter with name [{$name}] and provided configuration exists on the [{$livewireClass}] component.",
                );
            }

            return $this;
        };
    }

    public function assertTableFilterVisible(): Closure
    {
        return function (string $name): static {
            $name = $this->instance()->parseTableFilterName($name);

            $filter = $this->instance()->getTable()->getFilter(
                name: $name,
                withHidden: true,
            );

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $filter->isVisible(),
                "Failed asserting that a table filter with name [{$name}] is visible on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertTableFilterHidden(): Closure
    {
        return function (string $name): static {
            $name = $this->instance()->parseTableFilterName($name);

            $filter = $this->instance()->getTable()->getFilter(
                name: $name,
                withHidden: true,
            );

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $filter->isHidden(),
                "Failed asserting that a table filter with name [{$name}] is hidden on the [{$livewireClass}] component."
            );

            return $this;
        };
    }
}
