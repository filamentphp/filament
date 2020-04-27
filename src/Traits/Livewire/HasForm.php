<?php

namespace Filament\Traits\Livewire;

use Illuminate\Support\Arr;
use Filament\Contracts\Fieldset;

trait HasForm
{
    public $model;
    public $form_data;
    public $fieldset;

    public function setFieldset($className = null)
    {
        $class = $className ? $className : get_called_class().'Fieldset';
        foreach(config('filament.namespaces.fieldsets') as $namespace) {
            $class = $namespace.'\\'.class_basename($class);
            if (class_exists($class)) {
                if (!in_array(Fieldset::class, class_implements($class))) {
                    throw new \Error($class.' must implement '.Fieldset::class);
                }

                $this->fieldset = $class;
                break;
            }
        }
    }

    public function setFormProperties()
    {
        if ($this->model) {
            $this->form_data = $this->model->toArray();
        }

        foreach ($this->fields() as $field) {
            if (!isset($this->form_data[$field->name])) {
                $array = in_array($field->field_type, ['checkboxes', 'file']);
                $this->form_data[$field->name] = $field->default ?? ($array ? [] : null);
            }
        }
    }

    public function submit()
    {        
        $this->validate($this->rules());

        $field_names = [];
        foreach ($this->fields() as $field) {
            $field_names[] = $field->name;
        }
        $this->form_data = Arr::only($this->form_data, $field_names);

        $this->success();
    }

    public function success()
    {       
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('Success!'),
        ]);
    }

    public function save()
    {
        $this->submit();
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
    
    public function rules($realtime = false)
    {
        $rules = [];
        $rules_ignore = $realtime ? $this->rulesIgnoreRealtime() : [];

        foreach ($this->fields() as $field) {
            if ($field->rules) {
                $rules[$field->key] = $this->fieldRules($field, $rules_ignore);
            }
        }

        return $rules;
    }

    protected function fieldRules($field, $rules_ignore)
    {
        $field_rules = is_array($field->rules) ? $field->rules : explode('|', $field->rules);

        if ($rules_ignore) {
            $field_rules = collect($field_rules)->filter(function ($value, $key) use ($rules_ignore) {
                return !in_array($value, $rules_ignore);
            })->all();
        }
        
        return $field_rules;
    }

    public function updated($field)
    {
        $this->validateOnly($field, $this->rules(true));
    }

    public function rulesIgnoreRealtime()
    {
        return $this->fieldset ? $this->fieldset::rulesIgnoreRealtime() : [];
    }
}