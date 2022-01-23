<?php

namespace Filament\Resources;

class Table
{
    protected array $actions = [];

    protected array $bulkActions = [];

    protected array $columns = [];

    protected ?string $defaultSortColumn = null;

    protected ?string $defaultSortDirection = null;

    protected array $filters = [];

    protected array $headerActions = [];

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function actions(array $actions): static
    {
        $this->actions = $actions;

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

    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSortColumn = $column;
        $this->defaultSortDirection = $direction;

        return $this;
    }

    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function headerActions(array $actions): static
    {
        $this->headerActions = $actions;

        return $this;
    }

    public function prependActions(array $actions): static
    {
        $this->actions = array_merge($actions, $this->actions);

        return $this;
    }

    public function prependBulkActions(array $actions): static
    {
        $this->bulkActions = array_merge($actions, $this->bulkActions);

        return $this;
    }

    public function prependHeaderActions(array $actions): static
    {
        $this->headerActions = array_merge($actions, $this->headerActions);

        return $this;
    }

    public function pushActions(array $actions): static
    {
        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    public function pushBulkActions(array $actions): static
    {
        $this->bulkActions = array_merge($this->bulkActions, $actions);

        return $this;
    }

    public function pushHeaderActions(array $actions): static
    {
        $this->headerActions = array_merge($this->headerActions, $actions);

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function getColumns(): array
    {
        return $this->columns;
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

    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }
}
