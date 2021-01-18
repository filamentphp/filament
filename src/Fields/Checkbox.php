<?php

namespace Filament\Fields;

class Checkbox extends Field
{
    public $showErrors = true;

    public $type = 'checkbox';

    public function hideErrorOutput()
    {
        $this->showErrors = false;

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }
}
