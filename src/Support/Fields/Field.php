<?php

namespace Filament\Support\Fields;

use Illuminate\Support\Str;

class Field extends BaseField
{
    protected $label;
    protected $key;
    protected $file_multiple;
    protected $file_rules = ['file'];
    protected $file_validation_messages = ['file' => 'Must be a valid file.'];

    public function __construct($label, $name, $key_prefix = true)
    {
        $this->label = __($label);
        $this->name = $name ?? Str::snake(Str::lower($label));
        $this->key = $key_prefix ? 'form_data.' . $this->name : $this->name;
    }

    public static function make($label, $name = null, $key_prefix = true)
    {
        return new static($label, $name, $key_prefix);
    }

    public function file()
    {
        $this->type = 'file';
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

    public function multiple()
    {
        $this->file_multiple = true;
        return $this;
    }

    public function errorMessage($message)
    {
        return str_replace('form data.', '', $message);
    }
}
