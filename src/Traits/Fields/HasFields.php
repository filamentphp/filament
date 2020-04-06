<?php

namespace Filament\Traits\Fields;

use Filament\Contracts\Fieldset;

trait HasFields
{
    public function fields()
    {
        return [];
    }

    public function getFieldset($path)
    {
        $className = basename($path, '.php').'Fieldset';
        foreach(config('filament.namespaces.fieldsets') as $namespace) {
            $class = $namespace.'\\'.$className;
            if (class_exists($class)) {
                if (!in_array(Fieldset::class, class_implements($class))) {
                    throw new \Error($class.' must implement '.Fieldset::class);
                }

                return $class;
            }
        }

        throw new \Error($className.' not found.');
    }

    public function getField($field_name)
    {
        foreach ($this->fields() as $field) {
            if ($field->name == $field_name) {
                return $field;
                break;
            }
        }
    }

    public function saveField($field_name)
    {
        //
    }
}