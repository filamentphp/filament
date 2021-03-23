<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Column
{
    use Tappable;

    protected $configurationQueue = [];

    protected $getValueUsing;

    protected $isHidden = false;

    protected $isPrimary = false;

    protected $isSearchable = false;

    protected $isSortable = false;

    protected $label;

    protected $name;

    protected $table;

    protected $view;

    protected $viewData = [];

    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    protected function setUp()
    {
        //
    }

    public function configure($callback = null)
    {
        if ($callback === null) {
            foreach ($this->configurationQueue as $callback) {
                $callback();

                array_shift($this->configurationQueue);
            }

            return;
        }

        if ($this->getTable()) {
            $callback();
        } else {
            $this->configurationQueue[] = $callback;
        }

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        $this->configure(function () use ($callback, $contexts) {
            if (! is_array($contexts)) {
                $contexts = [$contexts];
            }

            if (! $callback) {
                $this->hidden();

                $callback = fn ($column) => $column->visible();
            }

            if (! $this->getContext() || in_array($this->getContext(), $contexts)) {
                return $this;
            }

            $callback($this);
        });

        return $this;
    }

    public function getContext()
    {
        return $this->getTable()->getContext();
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

    public function getLivewire()
    {
        return $this->getTable()->getLivewire();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTable()
    {
        return $this->table;
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
        $this->configure(function () use ($callback) {
            $this->getValueUsing = $callback;
        });

        return $this;
    }

    public function getView()
    {
        return $this->view;
    }

    public function hidden()
    {
        $this->configure(function () {
            $this->isHidden = true;
        });

        return $this;
    }

    public function isHidden()
    {
        return $this->isHidden;
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
        $this->configure(function () use ($label) {
            $this->label = $label;
        });

        return $this;
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function name($name)
    {
        $this->configure(function () use ($name) {
            $this->name = $name;
        });

        return $this;
    }

    public function only($contexts, $callback = null)
    {
        $this->configure(function () use ($callback, $contexts) {
            if (! is_array($contexts)) {
                $contexts = [$contexts];
            }

            if (! $callback) {
                $this->hidden();

                $callback = fn ($column) => $column->visible();
            }

            if (! in_array($this->getContext(), $contexts)) {
                return $this;
            }

            $callback($this);
        });

        return $this;
    }

    public function primary()
    {
        $this->configure(function () {
            $this->isPrimary = true;
        });

        return $this;
    }

    public function renderCell($record)
    {
        if ($this->isHidden()) {
            return;
        }

        $view = $this->getView() ?? 'tables::cells.' . Str::of(class_basename(static::class))->kebab();

        return view($view, array_merge($this->viewData, [
            'column' => $this,
            'record' => $record,
        ]));
    }

    public function searchable()
    {
        $this->configure(function () {
            $this->isSearchable = true;
        });

        return $this;
    }

    public function sortable()
    {
        $this->configure(function () {
            $this->isSortable = true;
        });

        return $this;
    }

    public function table($table)
    {
        $this->table = $table;

        $this->configure();

        return $this;
    }

    public function view($view, $data = [])
    {
        $this->configure(function () use ($data, $view) {
            $this->view = $view;

            $this->viewData($data);
        });

        return $this;
    }

    public function viewData($data = [])
    {
        $this->configure(function () use ($data) {
            $this->viewData = array_merge($this->viewData, $data);
        });

        return $this;
    }

    public function visible()
    {
        $this->configure(function () {
            $this->isHidden = false;
        });

        return $this;
    }
}
