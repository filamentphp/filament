<?php

namespace Filament\Traits\Fields;

trait HandlesArrays
{
    public function arrayAdd($field_name)
    {
        $array_fields = [];

        foreach ($this->fields() as $field) {
            if ($field->name == $field_name) {
                foreach ($field->array_fields as $array_field) {
                    $array_fields[$array_field->name] = $array_field->default ?? ($array_field->type == 'checkboxes' ? [] : null);
                }

                break;
            }
        }

        $this->form_data[$field_name][] = $array_fields;
        $this->updated('form_data.' . $field_name);
    }

    public function arrayMoveUp($field_name, $key)
    {
        if ($key > 0) {
            $prev = $this->form_data[$field_name][$key - 1];
            $this->form_data[$field_name][$key - 1] = $this->form_data[$field_name][$key];
            $this->form_data[$field_name][$key] = $prev;
        }
    }

    public function arrayMoveDown($field_name, $key)
    {
        if (($key + 1) < count($this->form_data[$field_name])) {
            $next = $this->form_data[$field_name][$key + 1];
            $this->form_data[$field_name][$key + 1] = $this->form_data[$field_name][$key];
            $this->form_data[$field_name][$key] = $next;
        }
    }

    public function arrayRemove($field_name, $key)
    {
        unset($this->form_data[$field_name][$key]);
        $this->form_data[$field_name] = array_values($this->form_data[$field_name]);
    }
}
