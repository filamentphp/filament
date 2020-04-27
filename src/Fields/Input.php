<?php

namespace Filament\Fields;

class Input extends Field
{
    protected $type = 'text';

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }
}