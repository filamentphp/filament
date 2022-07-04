<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
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
class TestsColumns
{
    public function assertCanRenderTableColumn(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnExists($name);

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
            $table = $this->instance();
            $tableClass = $table::class;

            $action = $table->getCachedTableColumn($name);

            Assert::assertInstanceOf(
                Column::class,
                $action,
                message: "Failed asserting that a table column with name [{$name}] exists on the [{$tableClass}] component.",
            );

            return $this;
        };
    }

    public function sortTable(): Closure
    {
        return function (?string $column = null, ?string $direction = null): static {
            $this->call('sortTable', $column, $direction);

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
