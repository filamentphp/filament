<?php

namespace Filament\Fields;

class Field extends BaseField
{
    public $error;

    public $extraAttributes = [];

    public $help;

    public $hint;

    public $label;

    public function error(string $error)
    {
        $this->error = $error;

        return $this;
    }

    public function extraAttributes(array $attributes)
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function help(string $help)
    {
        $this->help = $help;

        return $this;
    }

    public function hint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }
}
