<?php

namespace Filament\Traits\FieldConcerns;

trait CanBeAutofocused
{
    public $autofocus = false;

    public function autofocus()
    {
        $this->autofocus = true;

        return $this;
    }
}
