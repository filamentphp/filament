<?php

namespace Filament\Traits\Fields;

trait HasFields
{
    public function fields()
    {
        return [];
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