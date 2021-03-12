<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Column
{
    use Tappable;

    public $context;

    public $getValueUsing;

    public $hidden = false;

    public $label;

    public $name;

    public $primary = false;

    public $record;

    public $searchable = false;

    public $sortable = false;

    public $view;

    public $viewData = [];

    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    public static function make($name)
    {
        return new static($name);
    }

    protected function setUp()
    {
        //
    }

    public function context($context)
    {
        $this->context = $context;

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! $this->context || in_array($this->context, $contexts)) return $this;

        $callback($this);

        return $this;
    }

    public function getValue($record, $attribute = null)
    {
        if ($this->getValueUsing) {
            $callback = $this->getValueUsing;

            return $callback($record);
        }

        if ($attribute === null) {
            $attribute = $this->name;
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

    public function hidden()
    {
        $this->hidden = true;

        return $this;
    }

    public function isSearchable()
    {
        return $this->searchable && $this->getValueUsing === null;
    }

    public function isSortable()
    {
        return $this->sortable && $this->getValueUsing === null;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;

        $this->label(
            (string) Str::of($this->name)
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst(),
        );

        return $this;
    }

    public function only($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! in_array($this->context, $contexts)) return $this;

        $callback($this);

        return $this;
    }

    public function primary()
    {
        $this->primary = true;

        return $this;
    }

    public function searchable()
    {
        $this->searchable = true;

        return $this;
    }

    public function sortable()
    {
        $this->sortable = true;

        return $this;
    }

    public function view($view, $data = [])
    {
        $this->view = $view;

        $this->viewData($data);

        return $this;
    }

    public function viewData($data = [])
    {
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    public function visible()
    {
        $this->hidden = false;

        return $this;
    }

    public function renderCell($record)
    {
        if ($this->hidden) return;

        $view = $this->view ?? 'tables::cells.' . Str::of(class_basename(static::class))->kebab();

        return view($view, array_merge($this->viewData, [
            'column' => $this,
            'record' => $record,
        ]));
    }
}
