<?php

namespace Filament\Fields;

class Checkbox extends InputField
{
    public $showErrors = true;

    public $type = 'checkbox';

    public function hideErrors()
    {
        $this->showErrors = false;

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function showErrors()
    {
        $this->showErrors = true;

        return $this;
    }
}
