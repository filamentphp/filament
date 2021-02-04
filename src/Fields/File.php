<?php

namespace Filament\Fields;

class File extends InputField
{
    public $multiple;

    public function multiple()
    {
        $this->multiple = true;

        return $this;
    }

    public function single()
    {
        $this->multiple = false;

        return $this;
    }
}
