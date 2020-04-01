<?php

namespace Filament\Traits\Fields;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

trait UploadsFiles
{
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

        $storage_disk = self::$storage_disk ?? config('filament.storage_disk');
        $storage_path = self::$storage_path ?? config('filament.storage_path');
        $files = [];

        foreach (request()->file('files') as $file) {
            $files[] = [
                'file' => $file->store($storage_path, $storage_disk),
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
        foreach ($this->fields() as $field) {
            if ($field->name == $field_name) {
                $value = $field->file_multiple ? array_merge($this->form_data[$field_name], $uploaded_files) : $uploaded_files;
                break;
            }
        }

        $this->form_data[$field_name] = $value ?? [];
        $this->updated('form_data.' . $field_name);
    }

    public function fileIcon($mime_type)
    {
        $icons = [
            'image' => 'fa-file-image',
            'audio' => 'fa-file-audio',
            'video' => 'fa-file-video',
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.ms-word' => 'fa-file-word',
            'application/vnd.oasis.opendocument.text' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'fa-file-excel',
            'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel',
            'application/vnd.ms-powerpoint' => 'fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml' => 'fa-file-powerpoint',
            'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint',
            'text/plain' => 'fa-file-alt',
            'text/html' => 'fa-file-code',
            'application/json' => 'fa-file-code',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint',
            'application/gzip' => 'fa-file-archive',
            'application/zip' => 'fa-file-archive',
            'application/x-zip-compressed' => 'fa-file-archive',
            'application/octet-stream' => 'fa-file-archive',
        ];

        if (isset($icons[$mime_type])) return $icons[$mime_type];
        $mime_group = explode('/', $mime_type, 2)[0];

        return (isset($icons[$mime_group])) ? $icons[$mime_group] : 'fa-file';
    }
}
