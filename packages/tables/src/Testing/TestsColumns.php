<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
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

            $column = $this->instance()->getCachedTableColumn($name);

            $html = array_map(
                function ($record) use ($column) {
                    return $column->record($record)->toHtml();
                },
                $this->instance()->getTableRecords()->all(),
            );

            $this->assertSeeHtml($html);

            return $this;
        };
    }

    public function assertCanNotRenderTableColumn(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line  */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getCachedTableColumn($name);

            $html = array_map(
                function ($record) use ($column) {
                    return $column->record($record)->toHtml();
                },
                $this->instance()->getTableRecords()->all(),
            );

            $this->assertDontSeeHtml($html);

            return $this;
        };
    }

    public function assertTableColumnExists(): Closure
    {
        return function (string $name): static {
            $column = $this->instance()->getCachedTableColumn($name);

            $livewireClass = $this->instance()::class;

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

            $column = $this->instance()->getCachedTableColumn($name);

            $livewireClass = $this->instance()::class;

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

            $column = $this->instance()->getCachedTableColumn($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $column->isHidden(),
                message: "Failed asserting that a table column with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnStateSet(): Closure
    {
        return function (string $name, $value, $record): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getCachedTableColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $column->getState() == $value,
                message: "Failed asserting that a table column with name [{$name}] has value of [{$value}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnStateNotSet(): Closure
    {
        return function (string $name, $value, $record): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getCachedTableColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $column->getState() == $value,
                message: "Failed asserting that a table column with name [{$name}] does not have a value of [{$value}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function callTableColumnAction(): Closure
    {
        return function (string $name, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            if ($record instanceof Model) {
                $record = $this->instance()->getTableRecordKey($record);
            }

            $this->call('callTableColumnAction', $name, $record);

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
            $this->set('tableSearch', $search);

            return $this;
        };
    }
}
