<?php

namespace Filament\Forms\Components;

class Hidden extends Field
{
    public function value($value)
    {
        $this->default($value);

        return $this;
    }
}
