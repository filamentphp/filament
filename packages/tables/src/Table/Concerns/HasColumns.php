<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use InvalidArgumentException;

trait HasColumns
{
    /**
     * @var array<string, Column>
     */
    protected array $columns = [];

    /**
     * @var array<Column | ColumnLayoutComponent>
     */
    protected array $columnsLayout = [];

    protected ?ColumnLayoutComponent $collapsibleColumnsLayout = null;

    protected bool $hasColumnsLayout = false;

    /**
     * @param  array<Column | ColumnLayoutComponent>  $components
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
     * @param  array<Column | ColumnLayoutComponent>  $components
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

            if ($component instanceof Column) {
                $this->columns[$component->getName()] = $component;

                continue;
            }

            $this->hasColumnsLayout = true;

            $this->columns = [
                ...$this->columns,
                ...$component->getColumns(),
            ];
        }

        foreach ($this->columns as $column) {
            if ($column->hasSummary()) {
                $this->hasSummary = true;
            }

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
     * @return array<Column | ColumnLayoutComponent>
     */
    public function getColumnsLayout(): array
    {
        return $this->columnsLayout;
    }

    public function getCollapsibleColumnsLayout(): ?ColumnLayoutComponent
    {
        return $this->collapsibleColumnsLayout;
    }

    public function hasColumnsLayout(): bool
    {
        return $this->hasColumnsLayout;
    }
}
