<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutocompleted
{
    public $autocomplete;

    public function disableAutocomplete()
    {
        $this->autocomplete('off');

        return $this;
    }

    public function autocomplete($autocomplete = 'on')
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }
}
