<?php

namespace Filament\Tables\RecordActions;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Action
{
    use Tappable;

    public $name;

    public $view;

    public $viewData = [];

    public $when;

    public function __construct($name)
    {
        $this->name($name);

        $this->when(fn ($record) => true);

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

    public function name($name)
    {
        $this->name = $name;

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

    public function when($callback)
    {
        $this->when = $callback;

        return $this;
    }

    public function render($record)
    {
        $when = $this->when;

        if (! $when($record)) return;

        $view = $this->view ?? 'tables::record-actions.' . Str::of(class_basename(static::class))->kebab();

        return view($view, array_merge($this->viewData, [
            'record' => $record,
            'recordAction' => $this,
        ]));
    }
}
