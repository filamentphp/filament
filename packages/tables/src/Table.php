<?php

namespace Filament\Tables;

use Illuminate\Support\Traits\Tappable;

class Table
{
    use Tappable;

    protected $columns = [];

    protected $defaultSortColumn;

    protected $defaultSortDirection = 'asc';

    protected $filters = [];

    protected $livewire;

    protected $primaryColumnAction;

    protected $primaryColumnUrl;

    protected $recordActions = [];

    protected $reorderUsing;

    protected $shouldPrimaryColumnUrlOpenInNewTab = false;

    public function columns($columns)
    {
        $this->columns = collect(value($columns))
            ->map(function ($column) {
                return $column->table($this);
            })
            ->toArray();

        return $this;
    }

    public function defaultSort($column, $direction = 'asc')
    {
        $this->defaultSortColumn($column);
        $this->defaultSortDirection($direction);

        return $this;
    }

    public function defaultSortColumn($column)
    {
        $this->defaultSortColumn = $column;

        return $this;
    }

    public function defaultSortDirection($direction)
    {
        $this->defaultSortDirection = $direction;

        return $this;
    }

    public function filters($filters)
    {
        $this->filters = collect(value($filters))
            ->map(function ($filter) {
                return $filter->table($this);
            })
            ->toArray();

        return $this;
    }

    public static function for($livewire)
    {
        return (new static())->livewire($livewire);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getContext()
    {
        return get_class($this->getLivewire());
    }

    public function getDefaultSort()
    {
        return [$this->getDefaultSortColumn(), $this->getDefaultSortDirection()];
    }

    public function getDefaultSortColumn()
    {
        return $this->defaultSortColumn;
    }

    public function getDefaultSortDirection()
    {
        return $this->defaultSortDirection;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getLivewire()
    {
        return $this->livewire;
    }

    public function getPrimaryColumnAction($record = null)
    {
        $action = $this->primaryColumnAction;

        if ($record && is_callable($action)) {
            return $action($record);
        }

        return $action;
    }

    public function getPrimaryColumnUrl($record = null)
    {
        $url = $this->primaryColumnUrl;

        if ($record && is_callable($url)) {
            return $url($record);
        }

        return $url;
    }

    public function getRecordActions()
    {
        return $this->recordActions;
    }

    public function getVisibleColumns()
    {
        $columns = collect($this->getColumns())
            ->filter(fn ($column) => ! $column->isHidden())
            ->toArray();

        return $columns;
    }

    public function getVisibleFilters()
    {
        $filters = collect($this->getFilters())
            ->filter(fn ($filter) => ! $filter->isHidden())
            ->toArray();

        return $filters;
    }

    public function isReorderable()
    {
        return $this->reorderUsing !== null;
    }

    public function livewire($component)
    {
        $this->livewire = $component;

        return $this;
    }

    public function openPrimaryColumnUrlInNewTab()
    {
        $this->shouldPrimaryColumnUrlOpenInNewTab = true;

        return $this;
    }

    public function pagination($enabled)
    {
        $this->pagination = $enabled;

        return $this;
    }

    public function prependRecordActions($actions)
    {
        $this->recordActions = array_merge(
            collect(value($actions))->map(fn ($action) => $action->table($this))->toArray(),
            $this->recordActions,
        );

        return $this;
    }

    public function primaryColumnAction($action)
    {
        $this->primaryColumnAction = $action;

        return $this;
    }

    public function primaryColumnUrl($url, $shouldOpenInNewTab = false)
    {
        $this->primaryColumnUrl = $url;

        if ($shouldOpenInNewTab) {
            $this->openPrimaryColumnUrlInNewTab();
        }

        return $this;
    }

    public function pushRecordActions($actions)
    {
        $this->recordActions = array_merge(
            $this->recordActions,
            collect(value($actions))->map(fn ($action) => $action->table($this))->toArray(),
        );

        return $this;
    }

    public function recordActions($actions)
    {
        $this->recordActions = collect(value($actions))
            ->map(fn ($action) => $action->table($this))
            ->toArray();

        return $this;
    }

    public function reorder($order)
    {
        $callback = $this->reorderUsing;

        return $callback($order, $this->getLivewire());
    }

    public function reorderOn($column)
    {
        $this->reorderUsing(function ($order) use ($column) {
            foreach ($order as $item) {
                $record = $this->getLivewire()::getQuery()->find($item['value']);

                if (! $record) {
                    return;
                }

                $record->{$column} = $item['order'];
                $record->save();
            }
        });

        if ($this->getDefaultSortColumn() === null) {
            $this->defaultSortColumn($column);
        }

        return $this;
    }

    public function reorderUsing($callback)
    {
        $this->reorderUsing = $callback;

        return $this;
    }

    public function shouldPrimaryColumnUrlOpenInNewTab()
    {
        return $this->shouldPrimaryColumnUrlOpenInNewTab;
    }
}
