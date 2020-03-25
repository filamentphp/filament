<?php

namespace Filament\Support\Fields;

use Illuminate\Support\Str;

class Field extends BaseField
{
    protected $label;
    protected $key;
    protected $file_multiple;
    protected $array_fields = [];
    protected $array_sortable = false;

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

    public function multiple()
    {
        $this->file_multiple = true;
        return $this;
    }

    public function array($fields = [])
    {
        $this->type = 'array';
        $this->array_fields = $fields;
        return $this;
    }

    public function sortable()
    {
        $this->array_sortable = true;
        return $this;
    }

    public function errorMessage($message)
    {
        return str_replace('form data.', '', $message);
    }
}
