<?php

namespace Filament\Fields;

class Text extends Field
{
    public $type = 'text';

    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }
}
