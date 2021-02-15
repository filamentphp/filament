<?php

namespace Filament\Forms\Fields\Concerns;

trait CanBeAutofocused
{
    public $autofocus = false;

    public function autofocus()
    {
        $this->autofocus = true;

        return $this;
    }
}
