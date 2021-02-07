<?php

namespace Filament\Traits\FieldConcerns;

trait CanBeAutocompleted
{
    public $autocomplete;

    public function autocomplete($autocomplete = 'on')
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function disableAutocomplete()
    {
        $this->autocomplete('off');

        return $this;
    }
}
