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
        $this->model->$field_name = $this->form_data[$field_name];
        $this->model->save();
        
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $field_name]),
        ]);
    }
}