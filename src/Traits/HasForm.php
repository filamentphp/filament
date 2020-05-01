<?php

namespace Filament\Traits;

use Illuminate\Support\Arr;
use Filament\Contracts\Fieldset;

trait HasForm
{
    public $model;
    public $form_data;
    public $fieldset;

    /**
     * Setup our form, sets the model and 
     * corresponding data for the form.
     * 
     * @return void
     */
    public function initForm($model)
    {
        $this->model = $model;
        $this->form_data = $this->model->toArray();
    }

    /**
     * Save the form
     * 
     * @return void
     */
    public function save()
    {
        $this->submit();
    }

    /**
     * Submit and validate form
     * 
     * @throws Illuminate\Validation|ValidationException
     * @return void
     */
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

    /**
     * Successful form submission
     * 
     * @return void
     */
    public function success()
    {       
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('Success!'),
        ]);
    }

    /**
     * Save an individual field.
     * 
     * @var string $field_name
     * @return void
     */
    public function saveField(string $field_name)
    {
        $this->model->$field_name = $this->form_data[$field_name];
        $this->model->save();
        
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $field_name]),
        ]);
    }
    
    /**
     * Get all rules from the fieldset fields.
     * 
     * @var boolean $realtime
     * @return array
     */
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

    /**
     * Get the rules for a given field.
     * 
     * @var object $field
     * @var array $rules_ignore
     * 
     * @return array
     */
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

    /**
     * Runs after any update to the Livewire component's data
     * 
     * @var string $name
     * @return void
     */
    public function updated($name)
    {
        $this->validateOnly($name, $this->rules(true));
    }

    /**
     * Return fieldset classpath for a given called class
     * 
     * @return null|string
     */
    public function fieldset()
    {
        $baseName = class_basename(get_called_class()).'Fieldset';
        $namespace = collect(config('filament.namespaces.fieldsets'))->filter(function ($namespace, $key) use ($baseName) {
            return class_exists("{$namespace}\\{$baseName}");
        })->first();

        if ($namespace) {
            return "{$namespace}\\{$baseName}";
        }
    }

    /**
     * Return realtime rules to ignore from a given fieldset.
     * 
     * @return array
     */
    public function rulesIgnoreRealtime()
    {
        $rules = [];
        if ($fieldset = $this->fieldset()) {
            if (method_exists($fieldset, 'rulesIgnoreRealtime')) {
                $rules = call_user_func("{$fieldset}::rulesIgnoreRealtime");
            }
        }

        return $rules;
    }

    /**
     * Return the fields as a collection from a given fieldset.
     * 
     * @return Illuminate\Support\Collection
     */
    public function fields()
    {
        $fields = [];
        if ($fieldset = $this->fieldset()) {
            if (method_exists($fieldset, 'fields')) {
                $fields = call_user_func("{$fieldset}::fields", $this->model);
            }
        }

        return collect($fields);
    }

    /**
     * Get a field from the fields collection.
     * 
     * @var string $field_name
     * @return null|object
     */
    public function getField(string $field_name)
    {
        return $this->fields()->filter(function ($field, $key) use ($field_name) {
            return $field->name === $field_name;
        })->first();
    }

    /**
     * Return the meta fields as a collection from a given fieldset.
     * 
     * @return Illuminate\Support\Collection
     */
    public function metaFields()
    {
        $metaFields = [];
        if ($fieldset = $this->fieldset()) {
            if (method_exists($fieldset, 'metaFields')) {
                $metaFields = call_user_func("{$fieldset}::metaFields", $this->model);
            }
        }

        return collect($metaFields);
    }

    /**
     * Get a meta field from the metaFields collection.
     * 
     * @var string $field_name
     * @return null|object
     */
    public function getMetaField(string $field_name)
    {
        return $this->metaFields()->filter(function ($metaField, $key) use ($field_name) {
            return $metaField->name === $field_name;
        })->first();
    }
}