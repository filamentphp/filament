<?php

namespace Filament\Support\Fields;

use Illuminate\Support\Str;

class Field extends BaseField
{
    protected $id;
    protected $name;
    protected $label;
    protected $key;

    public function __construct($label, $name, $key_prefix = true)
    {
        $this->name = $name ?? Str::snake(Str::lower($label));
        $this->label = __($label);
        $this->key = $key_prefix ? 'form_data.'.$this->name : $this->name;
        $this->id = Str::uuid();
    }

    public static function make($label, $name = null, $key_prefix = true)
    {
        return new static($label, $name, $key_prefix);
    }
}
