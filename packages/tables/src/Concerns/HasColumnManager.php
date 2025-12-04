<?php

namespace Filament\Tables\Concerns;

use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use LogicException;

/**
 * @property-read Schema $toggleTableColumnForm
 */
trait HasColumnManager
{
    public const TABLE_COLUMN_MANAGER_GROUP_TYPE = 'group';

    public const TABLE_COLUMN_MANAGER_COLUMN_TYPE = 'column';

    /**
     * @var array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>
     */
    public array $tableColumns = [];

    /**
     * @var ?array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool,columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>
     */
    protected ?array $cachedDefaultTableColumnState = null;

    protected ?bool $hasReorderableTableColumns = null;

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
     * @return array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>
     */
    public function getDefaultTableColumnState(): array
    {
        return $this->cachedDefaultTableColumnState ??= collect($this->getTable()->getColumnsLayout())
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
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>|null  $state
     */
    public function applyTableColumnManager(?array $state = null, bool $wasReordered = false): void
    {
        if (filled($state)) {
            $this->tableColumns = $state;

            if ($this->hasReorderableTableColumns()) {
                $this->persistHasReorderedTableColumns($wasReordered);
            }
        }

        $this->hasReorderableTableColumns() && session()->get($this->getHasReorderedTableColumnsSessionKey())
            ? $this->syncReorderableColumnsFromDefaultTableColumnState()
            : $this->syncStaticColumnsFromTableColumnState();

        $this->persistTableColumns();
    }

    public function resetTableColumnManager(): void
    {
        $this->tableColumns = $this->getDefaultTableColumnState();

        if ($this->hasReorderableTableColumns()) {
            $this->updateTableColumns();
            $this->persistHasReorderedTableColumns();
        }

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

    public function getHasReorderedTableColumnsSessionKey(): string
    {
        $table = md5($this::class);

        return "tables.{$table}_has_reordered_columns";
    }

    /**
     * @return array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>
     */
    protected function loadTableColumnsFromSession(): array
    {
        return session()->get(
            $this->getTableColumnsSessionKey(),
            $this->getDefaultTableColumnState(),
        );
    }

    protected function persistTableColumns(): void
    {
        if ($this->getTable()->persistsColumnsInSession()) {
            session()->put(
                $this->getTableColumnsSessionKey(),
                $this->tableColumns
            );
        }
    }

    protected function persistHasReorderedTableColumns(bool $wasReordered = false): void
    {
        session()->put(
            $this->getHasReorderedTableColumnsSessionKey(),
            $wasReordered || $this->hasReorderedTableColumns()
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
     * @return array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}
     */
    protected function mapTableColumnGroupToArray(ColumnGroup $group): array
    {
        $label = e($group->getLabel());

        return [
            'type' => self::TABLE_COLUMN_MANAGER_GROUP_TYPE,
            'name' => $label,
            'label' => $label,
            'isHidden' => empty(array_filter($group->getColumns(), fn (Column $column): bool => ! $column->isHidden())),
            'isToggled' => true,
            'isToggleable' => true,
            'isToggledHiddenByDefault' => null,
            'columns' => array_values(
                array_map(
                    fn (Column $column): array => $this->mapTableColumnToArray($column),
                    $group->getColumns()
                )
            ),
        ];
    }

    /**
     * @return array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}
     */
    protected function mapTableColumnToArray(Column $column): array
    {
        $label = e($column->getLabel());

        if (blank($label) && $this->hasReorderableTableColumns()) {
            throw new LogicException("The table column [{$column->getName()}] has a blank label. All columns must have labels when they are reorderable.");
        }

        return [
            'type' => self::TABLE_COLUMN_MANAGER_COLUMN_TYPE,
            'name' => $column->getName(),
            'label' => $label,
            'isHidden' => $column->isHidden(),
            'isToggled' => ! $column->isToggleable() || ! $column->isToggledHiddenByDefault(),
            'isToggleable' => $column->isToggleable(),
            'isToggledHiddenByDefault' => $column->isToggleable() ? $column->isToggledHiddenByDefault() : null,
        ];
    }

    protected function syncReorderableColumnsFromDefaultTableColumnState(): void
    {
        $defaultColumnState = $this->getDefaultTableColumnState();

        $this->tableColumns = collect($this->tableColumns)
            ->map(fn (array $item) => $this->syncItemFromDefaultTableColumnState($item, $defaultColumnState))
            ->filter()
            ->values()
            ->merge($this->getNewDefaultColumnStateItems($defaultColumnState))
            ->all();

        $this->updateTableColumns();
    }

    protected function updateTableColumns(): void
    {
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
    }

    protected function syncStaticColumnsFromTableColumnState(): void
    {
        $this->tableColumns = collect($this->getDefaultTableColumnState())
            ->map(fn (array $item) => $this->syncItemFromTableColumnState($item, $this->tableColumns))
            ->all();
    }

    /**
     * @param  array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}  $item
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>  $defaultColumnState
     * @return array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}|null
     */
    protected function syncItemFromDefaultTableColumnState(array $item, array $defaultColumnState): ?array
    {
        $matchingItem = $this->findMatchingTableColumnStateItem($item, $defaultColumnState);

        if ($matchingItem === null) {
            return null;
        }

        $syncedItem = $this->syncTableColumnStateItemAttributes($item, $matchingItem);

        if ($syncedItem['type'] !== self::TABLE_COLUMN_MANAGER_GROUP_TYPE || ! isset($syncedItem['columns'])) {
            return $syncedItem;
        }

        $syncedItem['columns'] = $this->syncGroupFromDefaultTableColumnState(
            $syncedItem['columns'],
            $matchingItem['columns'] ?? []
        );

        if (empty($syncedItem['columns'])) {
            return null;
        }

        return $syncedItem;
    }

    /**
     * @param  array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}  $item
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>  $tableColumnState
     * @return array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}
     */
    protected function syncItemFromTableColumnState(array $item, array $tableColumnState): array
    {
        $matchingItem = $this->findMatchingTableColumnStateItem($item, $tableColumnState);

        if ($matchingItem === null) {
            return $item;
        }

        $syncedItem = $this->syncTableColumnStateItemAttributes($matchingItem, $item);

        if ($syncedItem['type'] !== self::TABLE_COLUMN_MANAGER_GROUP_TYPE || ! isset($syncedItem['columns'])) {
            return $syncedItem;
        }

        $syncedItem['columns'] = collect($item['columns'])
            ->map(fn (array $item) => $this->syncItemFromTableColumnState(
                $item,
                $matchingItem['columns'] ?? []
            ))
            ->all();

        return $syncedItem;
    }

    /**
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>  $existingColumns
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>  $defaultColumns
     * @return array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>
     */
    protected function syncGroupFromDefaultTableColumnState(array $existingColumns, array $defaultColumns): array
    {
        $updatedColumns = collect($existingColumns)
            ->map(function (array $column) use ($defaultColumns): ?array {
                $matchingDefault = $this->findMatchingTableColumnStateItem($column, $defaultColumns);

                if ($matchingDefault === null) {
                    return null;
                }

                return $this->syncTableColumnStateItemAttributes($column, $matchingDefault);
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
     * @param  array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}  $item
     * @param  array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}  $default
     * @return array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}
     */
    protected function syncTableColumnStateItemAttributes(array $item, array $default): array
    {
        $item['label'] = $default['label'];
        $item['isToggleable'] = $default['isToggleable'];
        $item['isHidden'] = $default['isHidden'];

        if (! $default['isToggleable']) {
            $item['isToggled'] = true;
        }

        if ($item['type'] === self::TABLE_COLUMN_MANAGER_COLUMN_TYPE) {
            if (
                $default['isToggleable'] &&
                is_null($item['isToggledHiddenByDefault'] ?? null) &&
                is_bool($default['isToggledHiddenByDefault'])
            ) {
                $item['isToggled'] = ! $default['isToggledHiddenByDefault'];
            }

            $item['isToggledHiddenByDefault'] = $default['isToggleable'] ? $default['isToggledHiddenByDefault'] : null;
        }

        return $item;
    }

    /**
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>  $defaultState
     * @return array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>
     */
    protected function getNewDefaultColumnStateItems(array $defaultState): array
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
     * @param  array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}  $item
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>  $items
     * @return array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}|null
     */
    protected function findMatchingTableColumnStateItem(array $item, array $items): ?array
    {
        return collect($items)
            ->first(
                fn (array $candidate) => $candidate['type'] === $item['type'] &&
                $candidate['name'] === $item['name']
            );
    }

    protected function hasReorderableTableColumns(): bool
    {
        return $this->hasReorderableTableColumns ??= $this->getTable()->hasReorderableColumns();
    }

    protected function hasReorderedTableColumns(): bool
    {
        $flattenedDefaultColumnState = $this->flattenTableColumnStateItems($this->getDefaultTableColumnState());
        $flattenedColumnState = $this->flattenTableColumnStateItems($this->tableColumns);

        $matchingDefaultColumns = collect($flattenedDefaultColumnState)
            ->filter(fn (string $key) => in_array($key, $flattenedColumnState))
            ->values()
            ->all();

        return $flattenedColumnState !== $matchingDefaultColumns;
    }

    /**
     * @param  array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool, columns?: array<int, array{type: string, name: string, label: string, isHidden: bool, isToggled: bool, isToggleable: bool, isToggledHiddenByDefault: ?bool}>}>  $items
     * @return array<int, string>
     */
    protected function flattenTableColumnStateItems(array $items): array
    {
        $flattenedItems = [];

        foreach ($items as $item) {
            $prefix = $item['type'] . ':' . $item['name'];
            $flattenedItems[] = $prefix;

            if ($item['type'] === self::TABLE_COLUMN_MANAGER_GROUP_TYPE && isset($item['columns'])) {
                foreach ($item['columns'] as $column) {
                    $flattenedItems[] = $prefix . ':' . $column['name'];
                }
            }
        }

        return $flattenedItems;
    }
}
