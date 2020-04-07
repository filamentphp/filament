<?php

namespace Filament\Support\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Filament\Traits\Fields\HasFields;
use Filament\Traits\Fields\FollowsRules;
use Filament\Traits\Fields\HandlesFiles;
use Filament\Traits\Fields\HandlesArrays;
use Filament\Contracts\Fieldset;

class FormComponent extends Component
{
    use AuthorizesRequests, HasFields, FollowsRules, HandlesFiles, HandlesArrays;

    public $model;
    public $fieldset;
    public $form_data;
    public $goback;

    protected $listeners = [
        'filament.fileUploadError' => 'fileUploadError',
        'filament.fileUpdate' => 'fileUpdate',
    ];

    public function setFieldset()
    {
        $className = (new \ReflectionClass(get_called_class()))->getShortName();
        $fieldsetName = $className.'Fieldset';
        foreach(config('filament.namespaces.fieldsets') as $namespace) {
            $class = $namespace.'\\'.$fieldsetName;
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
                $array = in_array($field->type, ['checkbox', 'file']);
                $this->form_data[$field->name] = $field->default ?? ($array ? [] : null);
            }
        }
    }

    public function updated($field)
    {
        $this->validateOnly($field, $this->rules(true));
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
        $this->emit('filament.notification.close');
        $this->submit();
    }

    public function saveAndGoBack()
    {
        $this->submit();
        $this->saveAndGoBackResponse();
    }

    public function saveAndGoBackResponse()
    {
        $path = Route::has($this->goback) ? route($this->goback) : $this->goback;
        return redirect($path);
    }
}
