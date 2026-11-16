<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\State\LazyLoadedState;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Renderless;
use ReflectionAttribute;
use ReflectionMethod;

trait HasColumns
{
    /**
     * @param  array<string, array{column: string, record: string}>  $targets
     * @return array<string, string>
     */
    #[Exposed]
    #[Renderless]
    public function loadTableColumnStates(array $targets): array
    {
        $components = [];
        $records = [];
        $rowLoops = [];
        $tableRecords = $this->getTableRecords();

        view()->addLoop($tableRecords);

        try {
            foreach ($tableRecords as $record) {
                view()->incrementLoopIndices();

                $recordKey = $this->getTableRecordKey($record);
                $records[$recordKey] = $record;
                $rowLoops[$recordKey] = view()->getLastLoop();
            }
        } finally {
            view()->popLoop();
        }

        foreach ($targets as $identifier => $target) {
            $column = $this->getTable()->getColumn($target['column']);
            $record = $records[$target['record']] ?? null;

            abort_unless($column && $record, 404);

            $column = (clone $column)
                ->record($record)
                ->recordKey($target['record'])
                ->rowLoop($rowLoops[$target['record']]);

            $component = $column;
            $layouts = [];

            while ($layout = $component->getLayout()) {
                $layout = (clone $layout)
                    ->record($record)
                    ->recordKey($target['record'])
                    ->rowLoop($rowLoops[$target['record']]);

                $component->layout($layout);
                $layouts[] = $layout;
                $component = $layout;
            }

            foreach (array_reverse($layouts) as $layout) {
                abort_if($layout->isHidden(), 403);
            }

            if ($this->getTable()->hasColumnsLayout()) {
                $column->inline();
            }

            abort_unless((! $column->isHidden()) && (! $column->isToggledHidden()) && $column->isStateLazyLoaded(), 403);

            $components[$identifier] = $column;
        }

        return app(LazyLoadedState::class)->render($components);
    }

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

        return $response;
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
