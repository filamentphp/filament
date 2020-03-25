<?php

namespace Filament\Support\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Filament\Traits\Fields\FollowsRules;
use Filament\Traits\Fields\HandlesArrays;
use Filament\Traits\Fields\UploadsFiles;

class FormComponent extends Component
{
    use FollowsRules, UploadsFiles, HandlesArrays;

    public $model;
    public $form_data;
    public $goback;
    private static $storage_disk;
    private static $storage_path;

    protected $listeners = ['fileUpdate'];

    public function mount($model = null, $goback = null)
    {
        $this->goback = $goback;
        $this->setFormProperties($model);
    }

    public function setFormProperties($model = null)
    {
        $this->model = $model;
        if ($model) $this->form_data = $model->toArray();

        foreach ($this->fields() as $field) {
            if (!isset($this->form_data[$field->name])) {
                $array = in_array($field->type, ['checkbox', 'file']);
                $this->form_data[$field->name] = $field->default ?? ($array ? [] : null);
            }
        }
    }

    public function render()
    {
        return $this->formView();
    }

    public function formView()
    {
        return view('filament::form', [
            'fields' => $this->fields(),
        ]);
    }

    public function fields()
    {
        return [
            // Field::make('Name')->input()->rules(['required', 'string', 'max:255']),
        ];
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

    public function errorMessage($message)
    {
        return str_replace('form data.', '', $message);
    }

    public function success()
    {
        $this->emit('notification.notify', [
            'type' => 'success',
            'message' => __('Success!'),
        ]);
    }

    public function saveAndStay()
    {
        $this->emit('notification.close');
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
