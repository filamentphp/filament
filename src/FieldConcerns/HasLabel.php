<?php

namespace Filament\FieldConcerns;

trait HasLabel
{
    public $label;

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }
}
