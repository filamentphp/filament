<?php

namespace Filament\Resources;

use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Arr;

class Table
{
    protected array $actions = [];

    protected ?string $actionsPosition = null;

    protected array $bulkActions = [];

    protected array $columns = [];

    protected ?string $defaultSortColumn = null;

    protected ?string $defaultSortDirection = null;

    protected array $filters = [];

    protected ?string $filtersLayout = null;

    protected array $headerActions = [];

    protected ?array $contentGrid = null;

    protected ?string $pollingInterval = null;

    protected ?string $reorderColumn = null;

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function actions(array | ActionGroup $actions, ?string $position = null): static
    {
        $this->actions = Arr::wrap($actions);
        $this->actionsPosition($position);

        return $this;
    }

    public function actionsPosition(?string $position = null): static
    {
        $this->actionsPosition = $position;

        return $this;
    }

    public function bulkActions(array $actions): static
    {
        $this->bulkActions = $actions;

        return $this;
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function contentGrid(?array $grid): static
    {
        $this->contentGrid = $grid;

        return $this;
    }

    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSortColumn = $column;
        $this->defaultSortDirection = strtolower($direction);

        return $this;
    }

    public function filters(array $filters, ?string $layout = null): static
    {
        $this->filters = $filters;
        $this->filtersLayout($layout);

        return $this;
    }

    public function filtersLayout(?string $filtersLayout): static
    {
        $this->filtersLayout = $filtersLayout;

        return $this;
    }

    public function headerActions(array | ActionGroup $actions): static
    {
        $this->headerActions = Arr::wrap($actions);

        return $this;
    }

    public function poll(?string $interval = '10s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function reorderable(?string $column = 'sort'): static
    {
        $this->reorderColumn = $column;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getActionsPosition(): ?string
    {
        return $this->actionsPosition;
    }

    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getContentGrid(): ?array
    {
        return $this->contentGrid;
    }

    public function getDefaultSortColumn(): ?string
    {
        return $this->defaultSortColumn;
    }

    public function getDefaultSortDirection(): ?string
    {
        return $this->defaultSortDirection;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFiltersLayout(): ?string
    {
        return $this->filtersLayout;
    }

    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }

    public function getReorderColumn(): ?string
    {
        return $this->reorderColumn;
    }

    public function getPollingInterval(): ?string
    {
        return $this->pollingInterval;
    }
}
