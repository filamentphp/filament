<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutofocused
{
    public $autofocus = false;

    public function autofocus()
    {
        $this->autofocus = true;

        return $this;
    }
}
