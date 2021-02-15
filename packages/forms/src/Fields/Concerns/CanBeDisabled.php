<?php

namespace Filament\Forms\Fields\Concerns;

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
