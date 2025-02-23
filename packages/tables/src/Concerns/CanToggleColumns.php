<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

/**
 * @property-read Schema $toggleTableColumnForm
 */
trait CanToggleColumns
{
    /**
     * @var array<string, bool | array<string, bool>>
     */
    public array $toggledTableColumns = [];

    /**
     * @return array<string, bool | array<string, bool>>
     */
    protected function getDefaultTableColumnToggleState(): array
    {
        $state = [];

        foreach ($this->getTable()->getColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            data_set($state, $column->getName(), ! $column->isToggledHiddenByDefault());
        }

        return $state;
    }

    public function updatedToggledTableColumns(): void
    {
        session()->put([
            $this->getTableColumnToggleFormStateSessionKey() => $this->toggledTableColumns,
        ]);
    }

    public function getTableColumnToggleForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('toggleTableColumnForm')) {
            return $this->getSchema('toggleTableColumnForm');
        }

        return $this->makeSchema()
            ->columns($this->getTable()->getColumnToggleFormColumns())
            ->live()
            ->schema($this->getTableColumnToggleFormSchema())
            ->statePath('toggledTableColumns');
    }

    /**
     * @return array<Checkbox>
     */
    protected function getTableColumnToggleFormSchema(): array
    {
        $schema = [];

        foreach ($this->getTable()->getColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            $schema[] = Checkbox::make($column->getName())
                ->label($column->getLabel());
        }

        return $schema;
    }

    public function isTableColumnToggledHidden(string $name): bool
    {
        return Arr::has($this->toggledTableColumns, $name) && ! data_get($this->toggledTableColumns, $name);
    }

    public function getTableColumnToggleFormStateSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_toggled_columns";
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return int | array<string, int | null>
     */
    protected function getTableColumnToggleFormColumns(): int | array
    {
        return 1;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableColumnToggleFormWidth(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableColumnToggleFormMaxHeight(): ?string
    {
        return null;
    }
}
