<?php

namespace Filament;

use Filament\View\Components\Fields;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Action extends Component
{
    public static $model;

    public static $resource;

    public static $title;

    public function callHooks($event)
    {
        return $this->getFields()->callHooks($event, $this);
    }

    public function fields()
    {
        return [];
    }

    public function getFields()
    {
        $record = null;
        if (property_exists($this, 'record')) $record = $this->record;

        return new Fields($this->fields(), $record);
    }

    public function getRules()
    {
        return $this->getFields()->getRules();
    }

    public static function getTitle()
    {
        if (static::$title) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function route($uri, $name)
    {
        return new ResourceRoute(static::class, $uri, $name);
    }

    public function rules()
    {
        return $this->getRules();
    }
}
