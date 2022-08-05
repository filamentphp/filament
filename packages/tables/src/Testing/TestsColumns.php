<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasTable instance()
 *
 * @mixin TestableLivewire
 */
class TestsColumns
{
    public function assertCanRenderTableColumn(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnVisible($name);

            $livewire = $this->instance();

            $column = $livewire->getCachedTableColumn($name);

            $html = array_map(
                function ($record) use ($column) {
                    return $column->record($record)->toHtml();
                },
                $livewire->getTableRecords()->all(),
            );

            $this->assertSeeHtml($html);

            return $this;
        };
    }

    public function assertTableColumnExists(): Closure
    {
        return function (string $name): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $column = $livewire->getCachedTableColumn($name);

            Assert::assertInstanceOf(
                Column::class,
                $column,
                message: "Failed asserting that a table column with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnVisible(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $column = $livewire->getCachedTableColumn($name);

            Assert::assertFalse(
                $column->isHidden(),
                message: "Failed asserting that a table column with name [{$name}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnHidden(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $column = $livewire->getCachedTableColumn($name);

            Assert::assertTrue(
                $column->isHidden(),
                message: "Failed asserting that a table column with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function sortTable(): Closure
    {
        return function (?string $name = null, ?string $direction = null): static {
            $this->call('sortTable', $name, $direction);

            return $this;
        };
    }

    public function searchTable(): Closure
    {
        return function (?string $search = null): static {
            $this->set('tableSearchQuery', $search);

            return $this;
        };
    }
}
