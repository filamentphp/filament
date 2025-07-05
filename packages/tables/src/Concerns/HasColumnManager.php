<?php

namespace Filament\Tables\Concerns;

use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;

/**
 * @property-read Schema $toggleTableColumnForm
 */
trait HasColumnManager
{
    public const TABLE_COLUMN_MANAGER_GROUP_TYPE = 'group';

    public const TABLE_COLUMN_MANAGER_COLUMN_TYPE = 'column';

    /**
     * @var array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>
     */
    public array $tableColumns = [];

    public function initTableColumnManager(): void
    {
        if ($this->getTable()->hasColumnsLayout()) {
            return;
        }

        if (blank($this->tableColumns)) {
            $this->tableColumns = $this->loadTableColumnsFromSession();
        }

        $this->applyTableColumnManager();
    }

    /**
     * @return array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>
     */
    public function getDefaultTableColumnManagerState(): array
    {
        return collect($this->getTable()->getColumnsLayout())
            ->map(fn (Component $component): ?array => match (true) {
                $component instanceof ColumnGroup => $this->mapTableColumnGroupToArray($component),
                $component instanceof Column => $this->mapTableColumnToArray($component),
                default => null,
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @deprecated Use `applyTableColumnManager()` instead.
     */
    public function updatedToggledTableColumns(): void
    {
        $this->applyTableColumnManager();
    }

    /**
     * @param  array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>|null  $state
     */
    public function applyTableColumnManager(?array $state = null): void
    {
        if (! $this->getTable()->hasReorderableColumns()) {
            return;
        }

        if (filled($state)) {
            $this->tableColumns = $state;
        }

        $this->syncTableColumnManagerWithDefaultState();

        $reorderedColumns = collect($this->tableColumns)
            ->map(function (array $item): Column | ColumnGroup | null {
                if ($item['type'] === self::TABLE_COLUMN_MANAGER_COLUMN_TYPE) {
                    return $this->getTable()->getColumn($item['name']);
                }

                if ($item['type'] !== self::TABLE_COLUMN_MANAGER_GROUP_TYPE || ! isset($item['columns'])) {
                    return null;
                }

                $columns = collect($item['columns'])
                    ->map(fn (array $column): ?Column => $this->getTable()->getColumn($column['name']))
                    ->filter()
                    ->all();

                if (empty($columns)) {
                    return null;
                }

                return $this->getTable()
                    ->getColumnGroup($item['name'])
                    ->columns($columns);
            })
            ->filter()
            ->all();

        $this->getTable()->columns($reorderedColumns);

        $this->persistTableColumns();
    }

    public function isTableColumnToggledHidden(string $name): bool
    {
        foreach ($this->tableColumns as $item) {
            if ($item['type'] === self::TABLE_COLUMN_MANAGER_COLUMN_TYPE && $item['name'] === $name) {
                return ! $item['isToggled'];
            }

            if ($item['type'] === self::TABLE_COLUMN_MANAGER_GROUP_TYPE && isset($item['columns'])) {
                foreach ($item['columns'] as $column) {
                    if ($column['name'] === $name) {
                        return ! $column['isToggled'];
                    }
                }
            }
        }

        return true;
    }

    /**
     * @deprecated Use `getTableColumnManagerSessionKey()` instead.
     */
    protected function getToggledTableColumnsSessionKey(): string
    {
        return $this->getTableColumnsSessionKey();
    }

    public function getTableColumnsSessionKey(): string
    {
        $table = md5($this::class);

        return "tables.{$table}_columns";
    }

    /**
     * @return array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>
     */
    protected function loadTableColumnsFromSession(): array
    {
        return session()->get(
            $this->getTableColumnsSessionKey(),
            $this->getDefaultTableColumnManagerState(),
        );
    }

    protected function persistTableColumns(): void
    {
        session()->put(
            $this->getTableColumnsSessionKey(),
            $this->tableColumns
        );
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

    /**
     * @return array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}
     */
    protected function mapTableColumnGroupToArray(ColumnGroup $group): array
    {
        return [
            'type' => self::TABLE_COLUMN_MANAGER_GROUP_TYPE,
            'name' => (string) $group->getLabel(),
            'label' => (string) $group->getLabel(),
            'isToggled' => true,
            'isToggleable' => true,
            'columns' => collect($group->getColumns())
                ->map(fn (Column $column): array => $this->mapTableColumnToArray($column))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}
     */
    protected function mapTableColumnToArray(Column $column): array
    {
        return [
            'type' => self::TABLE_COLUMN_MANAGER_COLUMN_TYPE,
            'name' => $column->getName(),
            'label' => (string) $column->getLabel(),
            'isToggled' => ! $column->isToggleable() || ! $column->isToggledHiddenByDefault(),
            'isToggleable' => $column->isToggleable(),
        ];
    }

    protected function syncTableColumnManagerWithDefaultState(): void
    {
        $defaultState = $this->getDefaultTableColumnManagerState();

        $this->tableColumns = collect($this->tableColumns)
            ->map(fn (array $item) => $this->syncTableColumnManagerItemWithDefaultState($item, $defaultState))
            ->filter()
            ->values()
            ->merge($this->getNewTableColumnManagerItems($defaultState))
            ->all();
    }

    /**
     * @param  array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}  $item
     * @param  array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>  $defaultState
     * @return array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}|null
     */
    protected function syncTableColumnManagerItemWithDefaultState(array $item, array $defaultState): ?array
    {
        $matchingDefault = $this->findMatchingTableColumnManagerItem($item, $defaultState);

        if ($matchingDefault === null) {
            return null;
        }

        $item = $this->applyDefaultStateToTableColumnManagerColumn($item, $matchingDefault);

        if ($item['type'] !== self::TABLE_COLUMN_MANAGER_GROUP_TYPE || ! isset($item['columns'])) {
            return $item;
        }

        $item['columns'] = $this->syncTableColumnManagerGroupColumnsWithDefaultState(
            $item['columns'],
            $matchingDefault['columns'] ?? []
        );

        if (empty($item['columns'])) {
            return null;
        }

        return $item;
    }

    /**
     * @param  array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>  $existingColumns
     * @param  array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>  $defaultColumns
     * @return array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>
     */
    protected function syncTableColumnManagerGroupColumnsWithDefaultState(array $existingColumns, array $defaultColumns): array
    {
        $updatedColumns = collect($existingColumns)
            ->map(function (array $column) use ($defaultColumns): ?array {
                $matchingDefault = $this->findMatchingTableColumnManagerItem($column, $defaultColumns);

                if ($matchingDefault === null) {
                    return null;
                }

                return $this->applyDefaultStateToTableColumnManagerColumn($column, $matchingDefault);
            })
            ->filter()
            ->values();

        $existingNames = $updatedColumns
            ->pluck('name')
            ->all();

        $newColumnsToAdd = collect($defaultColumns)
            ->reject(fn (array $column) => in_array($column['name'], $existingNames))
            ->values();

        return $updatedColumns
            ->merge($newColumnsToAdd)
            ->all();
    }

    /**
     * @param  array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}  $item
     * @param  array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}  $default
     * @return array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}
     */
    protected function applyDefaultStateToTableColumnManagerColumn(array $item, array $default): array
    {
        $item['label'] = $default['label'];
        $item['isToggleable'] = $default['isToggleable'];

        if (! $item['isToggleable']) {
            $item['isToggled'] = true;
        }

        return $item;
    }

    /**
     * @param  array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>  $defaultState
     * @return array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>
     */
    protected function getNewTableColumnManagerItems(array $defaultState): array
    {
        $existingKeys = collect($this->tableColumns)
            ->map(fn (array $item) => $item['type'] . ':' . $item['name'])
            ->all();

        return collect($defaultState)
            ->reject(fn (array $item) => in_array($item['type'] . ':' . $item['name'], $existingKeys))
            ->values()
            ->all();
    }

    /**
     * @param  array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}  $item
     * @param  array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}>  $items
     * @return array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool, columns?: array<int, array{type: string, name: string, label: string, isToggled: bool, isToggleable: bool}>}|null
     */
    protected function findMatchingTableColumnManagerItem(array $item, array $items): ?array
    {
        return collect($items)
            ->first(
                fn (array $candidate) => $candidate['type'] === $item['type'] &&
                $candidate['name'] === $item['name']
            );
    }
}
