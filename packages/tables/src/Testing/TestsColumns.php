<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Assert;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasTable instance()
 *
 * @mixin Testable
 */
class TestsColumns
{
    public function assertCanRenderTableColumn(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnVisible($name);

            $livewire = $this->instance();
            $livewireId = $livewire->getId();

            $html = array_map(
                function ($record) use ($livewire, $livewireId, $name): string {
                    return "wire:key=\"{$livewireId}.table.record.{$livewire->getTableRecordKey($record)}.column.{$name}\"";
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
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $livewire = $this->instance();
            $livewireId = $livewire->getId();

            $html = array_map(
                function ($record) use ($livewire, $livewireId, $name): string {
                    return "wire:key=\"{$livewireId}.table.record.{$livewire->getTableRecordKey($record)}.column.{$name}\"";
                },
                $this->instance()->getTableRecords()->all(),
            );

            $this->assertDontSeeHtml($html);

            return $this;
        };
    }

    public function assertTableColumnExists(): Closure
    {
        return function (string $name, ?Closure $checkColumnUsing = null, $record = null): static {
            $column = $this->instance()->getTable()->getColumn($name);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Column::class,
                $column,
                "Failed asserting that a table column with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            if ($record) {
                if (! ($record instanceof Model)) {
                    $record = $this->instance()->getTableRecord($record);
                }

                $column->record($record);
            }

            if ($checkColumnUsing) {
                Assert::assertTrue(
                    $checkColumnUsing($column),
                    "Failed asserting that a column with the name [{$name}] and provided configuration exists on the [{$livewireClass}] component."
                );
            }

            return $this;
        };
    }

    public function assertTableColumnDoesNotExist(): Closure
    {
        return function (string $name, ?Closure $checkColumnUsing = null, $record = null): static {
            $column = $this->instance()->getTable()->getColumn($name);

            $livewireClass = $this->instance()::class;

            if (! $column) {
                Assert::assertNull($column);

                return $this;
            }

            if ($record) {
                if (! ($record instanceof Model)) {
                    $record = $this->instance()->getTableRecord($record);
                }

                $column->record($record);
            }

            if ($checkColumnUsing) {
                Assert::assertFalse(
                    $checkColumnUsing($column),
                    "Failed asserting that a column with the name [{$name}] and provided configuration does not exist on the [{$livewireClass}] component."
                );
            } else {
                Assert::assertNotInstanceOf(
                    Column::class,
                    $column,
                    "Failed asserting that a table column with name [{$name}] does not exist on the [{$livewireClass}] component.",
                );
            }

            return $this;
        };
    }

    public function assertTableColumnVisible(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getTable()->getColumn($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $column->isHidden(),
                "Failed asserting that a table column with name [{$name}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnHidden(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getTable()->getColumn($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $column->isHidden(),
                "Failed asserting that a table column with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnStateSet(): Closure
    {
        return function (string $name, $state, $record): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $livewireClass = $this->instance()::class;

            $column->clearCachedState();

            $actualState = $column->getState();

            if (is_array($actualState)) {
                $actualState = json_encode($actualState);
            }

            if (is_array($state)) {
                $state = json_encode($state);
            }

            Assert::assertEquals(
                $state,
                $actualState,
                "Failed asserting that a table column with name [{$name}] has value of [{$state}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnStateNotSet(): Closure
    {
        return function (string $name, $state, $record): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $livewireClass = $this->instance()::class;

            $column->clearCachedState();

            $actualState = $column->getState();

            if (is_array($actualState)) {
                $actualState = json_encode($actualState);
            }

            if (is_array($state)) {
                $state = json_encode($state);
            }

            Assert::assertNotEquals(
                $state,
                $actualState,
                "Failed asserting that a table column with name [{$name}] does not have a value of [{$state}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnFormattedStateSet(): Closure
    {
        return function (string $name, $state, $record): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            /** @var TextColumn $column */
            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $livewireClass = $this->instance()::class;

            $column->clearCachedState();

            Assert::assertEquals(
                $state,
                $column->formatState($column->getState()),
                "Failed asserting that a table column with name [{$name}] has a formatted state of [{$state}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnFormattedStateNotSet(): Closure
    {
        return function (string $name, $state, $record): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            /** @var TextColumn $column */
            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $livewireClass = $this->instance()::class;

            $column->clearCachedState();

            Assert::assertNotEquals(
                $state,
                $column->formatState($column->getState()),
                "Failed asserting that a table column with name [{$name}] does not have a formatted state of [{$state}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnHasExtraAttributes(): Closure
    {
        return function (string $name, array $attributes, $record) {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $attributesString = print_r($attributes, true);

            $livewireClass = $this->instance()::class;

            Assert::assertEquals(
                $attributes,
                $column->getExtraAttributes(),
                "Failed asserting that a table column with name [{$name}] has extra attributes [{$attributesString}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnDoesNotHaveExtraAttributes(): Closure
    {
        return function (string $name, array $attributes, $record) {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $attributesString = print_r($attributes, true);

            $livewireClass = $this->instance()::class;

            Assert::assertNotEquals(
                $attributes,
                $column->getExtraAttributes(),
                "Failed asserting that a table column with name [{$name}] does not have extra attributes [{$attributesString}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnHasDescription(): Closure
    {
        return function (string $name, $description, $record, string $position = 'below') {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            /** @var TextColumn $column */
            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $actualDescription = $position === 'above' ? $column->getDescriptionAbove() : $column->getDescriptionBelow();

            $livewireClass = $this->instance()::class;

            Assert::assertEquals(
                $description,
                $actualDescription,
                "Failed asserting that a table column with name [{$name}] has description [{$description}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableColumnDoesNotHaveDescription(): Closure
    {
        return function (string $name, $description, $record, string $position = 'below') {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            /** @var TextColumn $column */
            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $actualDescription = $position === 'above' ? $column->getDescriptionAbove() : $column->getDescriptionBelow();

            $livewireClass = $this->instance()::class;

            Assert::assertNotEquals(
                $description,
                $actualDescription,
                "Failed asserting that a table column with name [{$name}] does not have description [{$description}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableSelectColumnHasOptions(): Closure
    {
        return function (string $name, array $options, $record) {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            /** @var SelectColumn $column */
            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $optionsString = print_r($options, true);

            $livewireClass = $this->instance()::class;

            Assert::assertEquals(
                $options,
                $column->getOptions(),
                "Failed asserting that a table column with name [{$name}] has options [{$optionsString}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableSelectColumnDoesNotHaveOptions(): Closure
    {
        return function (string $name, array $options, $record) {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

            /** @var SelectColumn $column */
            $column = $this->instance()->getTable()->getColumn($name);

            if (! ($record instanceof Model)) {
                $record = $this->instance()->getTableRecord($record);
            }

            $column->record($record);

            $optionsString = print_r($options, true);

            $livewireClass = $this->instance()::class;

            Assert::assertNotEquals(
                $options,
                $column->getOptions(),
                "Failed asserting that a table column with name [{$name}] does not have options [{$optionsString}] for record [{$record->getKey()}] on the [{$livewireClass}] component.",
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

    public function searchTableColumns(): Closure
    {
        return function (array $searches): static {
            $this->set('tableColumnSearches', $searches);

            return $this;
        };
    }
}
