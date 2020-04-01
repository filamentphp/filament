<?php

namespace Filament\Traits\Fields;

trait HandlesArrays
{
    public function arrayRemove($field_name, $key)
    {
        unset($this->form_data[$field_name][$key]);
        $this->form_data[$field_name] = array_values($this->form_data[$field_name]);
    }
}
