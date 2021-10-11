<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Column
{
    use Tappable;

    protected $getValueUsing;

    protected $isHidden = false;

    protected $isPrimary = false;

    protected $isSearchable = false;

    protected $isSortable = false;

    protected $label;

    protected $name;

    protected $searchColumns;

    protected $sortColumns;

    protected $table;

    protected $view;

    protected $viewData = [];

    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    protected function name($name)
    {
        $this->name = $name;

        return $this;
    }

    protected function setUp()
    {
        //
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst();
        }

        return $this->label;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLivewire()
    {
        return $this->getTable()->getLivewire();
    }

    public function getSearchColumns()
    {
        $columns = $this->searchColumns;

        if ($columns !== null) {
            if (! is_array($columns)) {
                $columns = [$columns];
            }

            if ($this->isRelationship()) {
                $columns = collect($columns)
                    ->map(fn ($column) => "{$this->getRelationshipName()}.{$column}")
                    ->toArray();
            }

            return $columns;
        }

        return [$this->getName()];
    }

    public function isRelationship()
    {
        return Str::of($this->getName())->contains('.');
    }

    public function getRelationshipName()
    {
        return Str::of($this->getName())->beforeLast('.');
    }

    public function getSortColumns()
    {
        $columns = $this->sortColumns;

        if ($columns !== null) {
            if (! is_array($columns)) {
                $columns = [$columns];
            }

            if ($this->isRelationship()) {
                $columns = collect($columns)
                    ->map(fn ($column) => "{$this->getRelationshipName()}.{$column}")
                    ->toArray();
            }

            return $columns;
        }

        return [$this->getName()];
    }

    public function getValue($record, $attribute = null)
    {
        $callback = $this->getValueUsing;

        if ($callback) {
            return $callback($record);
        }

        if ($attribute === null) {
            $attribute = $this->getName();
        }

        $value = $record->getAttribute(
            (string) Str::of($attribute)->before('.'),
        );

        if ($value instanceof Model) {
            $value = $this->getValue(
                $value,
                (string) Str::of($attribute)->after('.'),
            );
        }

        return $value;
    }

    public function getValueUsing($callback)
    {
        $this->getValueUsing = $callback;

        return $this;
    }

    public function isPrimary()
    {
        return $this->isPrimary;
    }

    public function isSearchable()
    {
        return $this->isSearchable && $this->getValueUsing === null;
    }

    public function isSortable()
    {
        return $this->isSortable && $this->getValueUsing === null;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function primary()
    {
        $this->isPrimary = true;

        return $this;
    }

    public function searchable($columns = [])
    {
        $this->isSearchable = true;

        if ($columns) {
            $this->searchColumns($columns);
        }

        return $this;
    }

    public function searchColumns($columns)
    {
        $this->searchColumns = $columns;

        return $this;
    }

    public function sortable($columns = [])
    {
        $this->isSortable = true;

        if ($columns) {
            $this->sortColumns($columns);
        }

        return $this;
    }

    public function sortColumns($columns)
    {
        $this->sortColumns = $columns;

        return $this;
    }
}
