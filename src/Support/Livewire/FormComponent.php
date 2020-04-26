<?php

namespace Filament\Support\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Filament\Traits\Fields\FollowsRules;
use Filament\Contracts\Fieldset;
use Filament\Models\Media;

class FormComponent extends Component
{
    use AuthorizesRequests, FollowsRules;

    public $model;
    public $fieldset;
    public $form_data;

    protected $listeners = [
        'filament.fileUploadError' => 'fileUploadError',
        'filament.fileUpdate' => 'fileUpdate',
    ];

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
                $array = in_array($field->type, ['checkboxes', 'file']);
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

    public static function fileUpload()
    {
        $field_name = request()->input('field_name');
        $validation_rules = json_decode(request()->input('validation_rules'), true);
        $validation_messages = json_decode(request()->input('validation_messages'), true);

        $validator = Validator::make(request()->all(), [
            'files.*' => $validation_rules,
        ], $validation_messages);

        if ($validator->fails()) {
            return [
                'field_name' => $field_name,
                'errors' => $validator->errors()->get('files.*'),
            ];
        }
        
        $files = [];
        $storage_disk = config('filament.storage_disk');

        foreach (request()->file('files') as $file) {
            $dir = config('filament.storage_path').'/'.Str::uuid();
            $files[] = [
                'dir' => $dir,
                'path' => $file->storeAs($dir, $file->getClientOriginalName(), $storage_disk),
                'name' => $file->getClientOriginalName(),
                'disk' => $storage_disk,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        }

        return ['field_name' => $field_name, 'uploaded_files' => $files];
    }

    public function fileUploadError($field_name, $error)
    {
        $message = is_array($error) ? collect(Arr::first($error))->implode(PHP_EOL) : $error;
        $field = $this->getField($field_name);
        if ($field) {
            $errorBag = $this->getErrorBag();
            $errorBag->add($field->key, $message);
        }
    }

    public function fileUpdate($field_name, $uploaded_files)
    {
        $media = [];
        foreach($uploaded_files as $file) {
            $media[] = new Media(['value' => $file]);
        }

        $this->model->media()->saveMany($media);
        
        $field = $this->getField($field_name);
        if ($field) {
            $media_ids = collect($media)->pluck('id')->all();
            $value = $field->multiple ? array_merge($this->form_data[$field_name], $media_ids) : $media_ids;
        }

        $this->form_data[$field_name] = $value ?? [];

        $this->saveField($field_name);
        $this->updated('form_data.'.$field_name);
    }

    public function fileRemove($field_name, $id, $key)
    {
        $media = Media::findOrFail($id);
        $media->delete();
        $this->arrayRemove($field_name, $key);
        $this->saveField($field_name);
        $this->updated('form_data.'.$field_name);
    }

    public function fileInfo($mime_type)
    {
        $icons = [
            'image' => 'heroicons/heroicon-o-photograph',
            'audio' => 'heroicons/heroicon-o-volume-up',
            'video' => 'heroicons/heroicon-o-ticket',
            'application/pdf' => 'heroicons/heroicon-o-book-open',
            'text/html' => 'heroicons/heroicon-o-code',
        ];

        if (isset($icons[$mime_type])) {
            return $icons[$mime_type];
        }

        $mime_group = explode('/', $mime_type, 2)[0];

        return [
            'is_image' => $mime_group === 'image',
            'icon' => (isset($icons[$mime_group])) ? $icons[$mime_group] : 'heroicons/heroicon-o-document',
        ];
    }

    public function arrayRemove($field_name, $key)
    {
        unset($this->form_data[$field_name][$key]);
        $this->form_data[$field_name] = array_values($this->form_data[$field_name]);
    }
}
