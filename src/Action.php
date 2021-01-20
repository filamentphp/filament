<?php

namespace Filament;

use Illuminate\Support\Str;
use Livewire\Component;

abstract class Action extends Component
{
    public $hasRouteParameter = true;

    protected $resource;

    public $title;

    public $record;

    public $records;

    protected static function getClassName()
    {
        return class_basename(static::class);
    }

    public function getTitle()
    {
        if ($this->title) return $this->title;

        return (string) Str::of(static::getClassName())
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }
}
