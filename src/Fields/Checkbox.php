<?php

namespace Filament\Fields;

class Checkbox extends Field {
    public $type = 'checkbox';

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }
}