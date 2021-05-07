<?php

namespace Filament\Tables\RecordActions;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Action
{
    use Tappable;

    protected $configurationQueue = [];

    protected $name;

    protected $table;

    protected $title;

    protected $view;

    protected $viewData = [];

    protected $when;

    public function __construct($name)
    {
        $this->name($name);

        $this->when(fn ($record) => true);

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

    public function getTitle()
    {
        if ($this->title === null) {
            return Str::of($this->name)
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return $this->title;
    }

    public function getView()
    {
        return $this->view;
    }

    public function getViewData()
    {
        return $this->viewData;
    }

    public static function make($name)
    {
        return new static($name);
    }

    protected function name($name)
    {
        $this->configure(function () use ($name) {
            $this->name = $name;
        });

        return $this;
    }

    public function render($record)
    {
        $when = $this->when;

        if (! $when($record)) {
            return;
        }

        $view = $this->getView() ?? 'tables::record-actions.' . Str::of(class_basename(static::class))->kebab();

        return view($view, array_merge($this->getViewData(), [
            'record' => $record,
            'recordAction' => $this,
        ]));
    }

    public function table($table)
    {
        $this->table = $table;

        $this->configure();

        return $this;
    }

    public function title($title)
    {
        $this->configure(function () use ($title) {
            $this->title = $title;
        });

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

    public function when($callback)
    {
        $this->configure(function () use ($callback) {
            $this->when = $callback;
        });

        return $this;
    }
}
