<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Attributes\Renderless;
use ReflectionAttribute;
use ReflectionMethod;

trait HasColumns
{
    #[Exposed]
    public function callTableColumnAction(string $name, string $recordKey): mixed
    {
        $record = $this->getTableRecord($recordKey);

        if (! $record) {
            return null;
        }

        $column = $this->getTable()->getColumn($name);

        if (! $column) {
            return null;
        }

        if ($column->isHidden()) {
            return null;
        }

        $action = $column->getAction();

        if (! ($action instanceof Closure)) {
            return null;
        }

        return $column->record($record)->evaluate($action);
    }

    #[Exposed]
    public function updateTableColumnState(string $column, string $record, mixed $input): mixed
    {
        $column = $this->getTable()->getColumn($column);

        if (! ($column instanceof Editable)) {
            return ['error' => __('filament-tables::table.columns.editable.unavailable')];
        }

        if ($column->isHidden()) {
            return ['error' => __('filament-tables::table.columns.editable.unavailable')];
        }

        $record = $this->getTableRecord($record);

        if (! $record) {
            return ['error' => __('filament-tables::table.columns.editable.unavailable')];
        }

        $column->record($record);

        if ($column->isDisabled()) {
            return ['error' => __('filament-tables::table.columns.editable.unavailable')];
        }

        try {
            $column->validate($input);
        } catch (ValidationException $exception) {
            return [
                'error' => $exception->getMessage(),
            ];
        }

        $response = $column->updateState($input);

        $column->clearCachedState();
        $this->flushCachedTableRecords();

        $this->partiallyRenderTableColumnsAfterStateUpdated($column, $record);

        return $response;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    protected function partiallyRenderTableColumnsAfterStateUpdated(Column $column, Model | array $record): void
    {
        if (
            (! method_exists($column, 'getColumnsToPartiallyRenderAfterStateUpdated')) ||
            (! method_exists($column, 'isPartiallyRenderedAfterStateUpdated'))
        ) {
            return;
        }

        $columnNames = $column->getColumnsToPartiallyRenderAfterStateUpdated();

        if (blank($columnNames) && (! $column->isPartiallyRenderedAfterStateUpdated())) {
            return;
        }

        $table = $this->getTable();

        // Cells are only rendered in a `<td>` that can be replaced when the table does not use a content layout.
        if ($table->getContent() || $table->hasColumnsLayout()) {
            return;
        }

        $columnNames = array_unique([$column->getName(), ...$columnNames]);

        foreach ($columnNames as $columnName) {
            if (! $table->getColumn($columnName)) {
                throw new InvalidArgumentException("Could not find column [{$columnName}] to partially render.");
            }
        }

        $recordKey = $this->getTableRecordKey($record);

        app(PartialsComponentHook::class)->renderPartial($this, function () use ($columnNames, $recordKey): array {
            $record = $this->getTableRecord($recordKey);

            if (! $record) {
                return [];
            }

            $table = $this->getTable();
            $visibleColumns = $table->getVisibleColumns();

            $recordAction = $table->getRecordAction($record);
            $recordUrl = $table->getRecordUrl($record);
            $shouldOpenRecordUrlInNewTab = $table->shouldOpenRecordUrlInNewTab($record);

            $partials = [];

            foreach ($columnNames as $columnName) {
                $column = $visibleColumns[$columnName] ?? null;

                if (! $column) {
                    continue;
                }

                $column->record($record);
                $column->recordKey($recordKey);
                $column->clearCachedState();

                $partials[$column->getTableCellPartialName()] = $column->renderInTableCell(
                    recordAction: $recordAction,
                    recordUrl: $recordUrl,
                    shouldOpenRecordUrlInNewTab: $shouldOpenRecordUrlInNewTab,
                    isPartiallyRenderable: true,
                );
            }

            return $partials;
        });
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    #[Exposed]
    public function callTableColumnMethod(string $name, string $recordKey, string $method, array $arguments = []): mixed
    {
        // Security: This method is callable from the frontend and dispatches
        // to `#[Exposed]` methods on table columns. It does
        // not perform per-record policy checks. Inline editable columns
        // called through here bypass Model Policies.

        $column = $this->getTable()->getColumn($name);

        if (! $column) {
            return null;
        }

        if ($column->isHidden()) {
            return null;
        }

        if (! method_exists($column, $method)) {
            return null;
        }

        $methodReflection = new ReflectionMethod($column, $method);

        if (! $methodReflection->getAttributes(Exposed::class, ReflectionAttribute::IS_INSTANCEOF)) {
            return null;
        }

        if ($methodReflection->getAttributes(Renderless::class)) {
            $this->skipRender();
        }

        $record = $this->getTableRecord($recordKey);

        if (! $record) {
            return null;
        }

        $column->record($record);

        return $column->{$method}(...$arguments);
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<Column | ColumnLayoutComponent>
     */
    protected function getTableColumns(): array
    {
        return [];
    }
}
