<?php

namespace Filament\Traits\Fields;

trait HasFields
{
    public function fields()
    {
        return [];
    }

    public function getField($name)
    {
        foreach ($this->fields() as $field) {
            if ($field->name == $name) {
                return $field;
                break;
            }
        }
    }
}