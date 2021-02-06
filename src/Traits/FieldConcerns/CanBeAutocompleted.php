<?php

namespace Filament\Traits\FieldConcerns;

trait CanBeAutocompleted
{
    public $autocomplete = false;

    public function autocomplete($key = null)
    {
        $this->autocomplete = $key !== null ? $key : true;

        return $this;
    }
}
