<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Column
{
    use Tappable;

    public $getValueUsing;

    public $hidden = false;

    public $id;

    public $label;

    public $name;

    public $searchable = false;

    public $sortable = false;

    protected $context;

    protected $pendingExcludedContextModifications = [];

    protected $pendingIncludedContextModifications = [];

    protected $record;

    protected $view;

    public function __construct($name)
    {
        $this->name($name);
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function context($context)
    {
        $this->context = $context;

        if (array_key_exists($this->context, $this->pendingIncludedContextModifications)) {
            foreach ($this->pendingIncludedContextModifications[$this->context] as $callback) {
                $callback($this);
            }
        }

        $this->pendingIncludedContextModifications = [];

        collect($this->pendingExcludedContextModifications)
            ->filter(fn ($callbacks, $context) => $context !== $this->context)
            ->each(function ($callbacks) {
                foreach ($callbacks as $callback) {
                    $callback($this);
                }
            });

        $this->pendingExcludedContextModifications = [];

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! $this->context) {
            foreach ($contexts as $context) {
                if (! array_key_exists($context, $this->pendingExcludedContextModifications)) {
                    $this->pendingExcludedContextModifications[$context] = [];
                }

                $this->pendingExcludedContextModifications[$context][] = $callback;
            }

            return $this;
        }

        if (in_array($this->context, $contexts)) return $this;

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
                (string) Str::of($attribute)->after('.')
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

    public function id($id)
    {
        $this->id = $id;

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

        $this->id(
            (string) Str::of($this->name)
                ->replace('.', '-')
                ->slug(),
        );

        $this->label(
            (string) Str::of($this->name)
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst(),
        );
    }

    public function only($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! $this->context) {
            foreach ($contexts as $context) {
                if (! array_key_exists($context, $this->pendingIncludedContextModifications)) {
                    $this->pendingIncludedContextModifications[$context] = [];
                }

                $this->pendingIncludedContextModifications[$context][] = $callback;
            }

            return $this;
        }

        if (! in_array($this->context, $contexts)) return $this;

        $callback($this);

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

    public function view($view)
    {
        $this->view = $view;

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

        return view($view, [
            'column' => $this,
            'record' => $record,
        ]);
    }
}
