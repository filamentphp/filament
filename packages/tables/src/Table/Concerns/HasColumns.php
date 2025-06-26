<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use InvalidArgumentException;

trait HasColumns
{
    /**
     * @var array<string, Column>
     */
    protected array $columns = [];

    /**
     * @var array<Column | ColumnLayoutComponent | ColumnGroup>
     */
    protected array $columnsLayout = [];

    protected ?ColumnLayoutComponent $collapsibleColumnsLayout = null;

    protected bool $hasColumnGroups = false;

    protected bool $hasColumnsLayout = false;

    /**
     * @param  array<Column | ColumnLayoutComponent | ColumnGroup>  $components
     */
    public function columns(array $components): static
    {
        $this->columns = [];
        $this->columnsLayout = [];
        $this->collapsibleColumnsLayout = null;
        $this->hasColumnsLayout = false;
        $this->pushColumns($components);

        return $this;
    }

    /**
     * @param  array<Column | ColumnLayoutComponent | ColumnGroup>  $components
     */
    public function pushColumns(array $components): static
    {
        foreach ($components as $component) {
            $component->table($this);

            if ($component instanceof ColumnLayoutComponent && $component->isCollapsible()) {
                $this->collapsibleColumnsLayout = $component;
            } else {
                $this->columnsLayout[] = $component;
            }

            if ($component instanceof ColumnGroup) {
                $this->hasColumnGroups = true;

                $this->columns = [
                    ...$this->columns,
                    ...$component->getColumns(),
                ];

                continue;
            }

            if ($component instanceof ColumnLayoutComponent) {
                $this->hasColumnsLayout = true;

                $this->columns = [
                    ...$this->columns,
                    ...$component->getColumns(),
                ];

                continue;
            }

            $this->columns[$component->getName()] = $component;
        }

        foreach ($this->columns as $column) {
            $action = $column->getAction();

            if (($action === null) || ($action instanceof Closure)) {
                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Table column actions must be an instance of ' . Action::class . '.');
            }

            $this->cacheAction($action->table($this));
        }

        return $this;
    }

    /**
     * @return array<string, Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<string, Column>
     */
    public function getVisibleColumns(): array
    {
        return array_filter(
            $this->getColumns(),
            fn (Column $column): bool => $column->isVisible() && (! $column->isToggledHidden()),
        );
    }

    public function getColumn(string $name): ?Column
    {
        return $this->getColumns()[$name] ?? null;
    }

    /**
     * @return array<Column | ColumnLayoutComponent | ColumnGroup>
     */
    public function getColumnsLayout(): array
    {
        return $this->columnsLayout;
    }

    public function getCollapsibleColumnsLayout(): ?ColumnLayoutComponent
    {
        return $this->collapsibleColumnsLayout;
    }

    public function hasColumnGroups(): bool
    {
        if (! $this->hasColumnGroups) {
            return false;
        }

        foreach ($this->getVisibleColumns() as $column) {
            $columnGroup = $column->getGroup();

            if (! $columnGroup) {
                continue;
            }

            if (empty($columnGroup->getVisibleColumns())) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function hasColumnsLayout(): bool
    {
        return $this->hasColumnsLayout;
    }
}
