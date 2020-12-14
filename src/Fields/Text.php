<?php

namespace Filament\Fields;

class Text extends Field {
    public $type = 'text';

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }
}