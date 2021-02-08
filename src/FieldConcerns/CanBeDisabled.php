<?php

namespace Filament\FieldConcerns;

trait CanBeDisabled
{
    public $disabled = false;

    public function disabled()
    {
        $this->disabled = true;

        return $this;
    }

    public function enabled()
    {
        $this->disabled = false;

        return $this;
    }
}
