<?php

namespace Filament;

use Filament\Fields\InputField;
use Filament\Fields\Tab;
use Filament\Traits\WithNotifications;
use Filament\View\Components\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

abstract class Action extends Component
{
    use WithNotifications;

    protected static $resource;

    protected static $title;

    public static function getModel()
    {
        $resource = static::getResource();

        return $resource::$model;
    }

    public static function getResource()
    {
        return static::$resource;
    }

    public static function route($uri, $name)
    {
        return new ResourceRoute(static::class, $uri, $name);
    }

    protected static function getTitle()
    {
        if (static::$title) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function reset(...$properties)
    {
        parent::reset(...$properties);

        $defaults = $this->getPropertyDefaults();

        if (count($properties) && is_array($properties[0])) $properties = $properties[0];

        if (empty($properties)) $properties = array_keys($defaults);

        $propertiesToFill = collect($properties)
            ->filter(fn ($property) => in_array($property, $defaults))
            ->mapWithKeys(fn ($property) => [$property => $defaults[$property]])
            ->toArray();

        $this->fill($propertiesToFill);
    }

    protected function callFormHooks($event)
    {
        return $this->getForm()->callHook($this, $event);
    }

    public function getForm()
    {
        $record = null;
        if (property_exists($this, 'record') && $this->record instanceof Model) $record = $this->record;

        return new Form(
            $this->getFields(),
            static::class,
            $record,
        );
    }

    protected function getFields()
    {
        $fields = static::$resource ? static::$resource::fields() : [];

        if (method_exists($this, 'fields')) return array_merge($fields, $this->fields());

        return $fields;
    }

    protected function fillWithFormDefaults()
    {
        $this->fill($this->getPropertyDefaults());
    }

    protected function getPropertyDefaults()
    {
        return $this->getForm()->getDefaults();
    }

    protected function getRules()
    {
        $rules = $this->getForm()->getRules();

        foreach (parent::getRules() as $field => $conditions) {
            if (! is_array($conditions)) $conditions = explode('|', $conditions);

            $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
        }

        return $rules;
    }

    protected function getValidationAttributes()
    {
        $attributes = $this->getForm()->getValidationAttributes();

        foreach (parent::getValidationAttributes() as $name => $label) {
            $attributes[$name] = $label;
        }

        return $attributes;
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getFields())
                ->first(function ($field) use ($exception) {
                    return ($field instanceof InputField &&
                        array_key_exists($field->name, $exception->validator->failed())
                    );
                });

            if ($fieldToFocus && $fieldToFocus->parentField instanceof Tab) {
                $tabToFocus = $fieldToFocus->parentField;

                $this->dispatchBrowserEvent(
                    'switch-tab',
                    $tabToFocus->parentField->id . '.' . $tabToFocus->id,
                );
            }

            throw $exception;
        }
    }
}
