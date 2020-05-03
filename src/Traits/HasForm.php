<?php

namespace Filament\Traits;

use Illuminate\Support\Arr;
use Filament\Contracts\Fieldset;

trait HasForm
{
    public $model;
    public $model_data;
    public $meta_data;

    /**
     * Setup our form, sets the model (if provided) and 
     * corresponding data for the form.
     * 
     * @var mixed $model
     * @return void
     */
    public function initForm($model = null)
    {
        $this->model = $model;
        $this->setModelData();
        $this->setMetaData();
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
        if ($field = $this->getField($field_name)) {
            if ($field->is_meta) {
                $this->model->setMeta($field->name, $this->meta_data[$field->name]);
            } else {
                $this->model->$field_name = $this->model_data[$field->name];
                $this->model->save();
            }

            $this->emit('filament.notification.notify', [
                'type' => 'success',
                'message' => __('filament::notifications.updated', ['item' => $field->name]),
            ]);
        }
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

    protected function setModelData()
    {
        $data = $this->model ? $this->model->toArray() : [];

        foreach($this->fields() as $field) {
            if (!isset($data[$field->name]) && !$field->is_meta) {
                $data[$field->name] = $field->default;
            }
        }

        $this->model_data = $data;
    }

    protected function setMetaData()
    {
        $data = $this->model ? $this->model->getAllMeta()->toArray() : [];

        foreach($this->fields() as $field) {
            if (!isset($data[$field->name]) && $field->is_meta) {
                $data[$field->name] = $field->default;
            }
        }

        $this->meta_data = $data;
    }
}