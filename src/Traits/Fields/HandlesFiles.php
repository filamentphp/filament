<?php

namespace Filament\Traits\Fields;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

trait HandlesFiles
{
    use HandlesArrays;

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
            $files[] = [
                'file' => $file->store(config('filament.storage_path'), $storage_disk),
                'disk' => $storage_disk,
                'name' => $file->getClientOriginalName(),
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
        $field = $this->getField($field_name);
        if ($field) {
            $value = $field->file_multiple ? array_merge($this->form_data[$field_name], $uploaded_files) : $uploaded_files;
        }

        $this->form_data[$field_name] = $value ?? [];

        $this->saveField($field_name);
        $this->updated('form_data.'.$field_name);
    }

    public function fileRemove($field_name, $file_name, $key)
    {
        $files = $this->form_data[$field_name];
        foreach($files as $file) {
            $storage = Storage::disk($file['disk']);
            if ($storage->exists($file['file']) && $file['name'] === $file_name) {
                $storage->delete($file['file']);
                break;
            }
        }
        
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
}
