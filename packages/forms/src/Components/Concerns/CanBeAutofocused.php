<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutofocused
{
    protected $autofocus = false;

    public function autofocus()
    {
        $this->autofocus = true;

        return $this;
    }

    public function getAutofocus()
    {
        return $this->autofocus;
    }
}
