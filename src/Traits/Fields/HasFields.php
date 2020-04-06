<?php

namespace Filament\Traits\Fields;

trait HasFields
{
    public function fields()
    {
        return $this->fieldset ? $this->fieldset::fields($this->model) : [];
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