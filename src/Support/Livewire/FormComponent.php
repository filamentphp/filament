<?php

namespace Filament\Support\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Filament\Traits\Fields\FollowsRules;
use Filament\Traits\Fields\HasFields;
use Filament\Traits\Fields\HandlesFiles;
use Filament\Traits\Fields\HandlesArrays;

class FormComponent extends Component
{
    use FollowsRules, HasFields, HandlesFiles, HandlesArrays;

    public $model;
    public $form_data;
    public $goback;
    private static $storage_disk;
    private static $storage_path;

    protected $listeners = [
        'filament.fileUploadError' => 'fileUploadError',
        'filament.fileUpdate' => 'fileUpdate',
    ];

    public function mount($model = null, $goback = null)
    {
        $this->setFormProperties($model);
        $this->goback = $goback;
    }

    public function setFormProperties($model = null)
    {
        $this->model = $model;
        if ($model) {
            $this->form_data = $model->toArray();
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
