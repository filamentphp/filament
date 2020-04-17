<?php

namespace Filament\Support\Fields;

use Illuminate\Support\Str;

class Field extends BaseField
{
    protected $name;
    protected $label;
    protected $key;
    protected $id;

    public function __construct($name, $label = null, $key = null)
    {
        $this->name = $name;
        $this->label = is_null($label) ? $this->formatLabel($name) : $label;
        $this->key = $key ?? 'form_data.'.$this->name;
        $this->id = Str::slug($this->key);
    }

    public static function make($name, $label = null, $key = null)
    {
        return new static($name, $label, $key);
    }

    protected function formatLabel($value)
    {
        return Str::of($value)
            ->replaceMatches('/[\-_]/', ' ')
            ->title()
            ->__toString();
    }
}
