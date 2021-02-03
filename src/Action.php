<?php

namespace Filament;

use Filament\View\Components\Form;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Action extends Component
{
    public static $model;

    public static $resource;

    public static $title;

    public function callFormHooks($event)
    {
        return $this->getForm()->callActionHooks($this, $event);
    }

    public function fields()
    {
        return static::$resource::fields();
    }

    public function getForm()
    {
        $record = null;
        if (property_exists($this, 'record')) $record = $this->record;

        return new Form($this->fields(), $record);
    }

    public function getRules()
    {
        return $this->getForm()->getRules();
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
