<?php

namespace Filament\Fields;

class File extends Field
{
    protected $file_rules = ['file'];
    protected $file_validation_messages = ['file' => 'Must be a valid file.'];
    protected $files = [];

    public function files($files)
    {
        $this->files = (array) $files;
        return $this;
    }

    public function fileRules($rules)
    {
        $this->file_rules = (array) $rules;
        return $this;
    }

    public function fileValidationMessages(array $messages)
    {
        $this->file_validation_messages = $messages;
        return $this;
    }
}