<?php

namespace Filament;

use Filament\Traits\WithNotifications;
use Filament\View\Components\Form;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Action extends Component
{
    use WithNotifications;

    protected static $resource;

    protected static $title;

    protected function callFormHooks($event)
    {
        return $this->getForm()->callHook($this, $event);
    }

    public function fields()
    {
        return static::$resource::fields();
    }

    public function getForm()
    {
        return new Form($this->fields(), static::class);
    }

    public static function getModel()
    {
        $resource = static::getResource();

        return $resource::$model;
    }

    public static function getResource()
    {
        return static::$resource;
    }

    protected function getRules()
    {
        $rules = $this->getForm()->getRules();

        collect(parent::getRules())
            ->each(function ($conditions, $field) use (&$rules) {
                if (! is_array($conditions)) $conditions = explode('|', $conditions);

                $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
            });

        return $rules;
    }

    public static function getTitle()
    {
        if (static::$title) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    protected function getValidationAttributes()
    {
        $attributes = $this->getForm()->getValidationAttributes();

        collect(parent::getValidationAttributes())
            ->each(function ($label, $name) use (&$attributes) {
                $attributes[$name] = $label;
            });

        return $attributes;
    }

    public static function route($uri, $name)
    {
        return new ResourceRoute(static::class, $uri, $name);
    }
}
