<?php

namespace Filament\Traits\FieldConcerns;

trait CanHaveLabel
{
    public $label;

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }
}
