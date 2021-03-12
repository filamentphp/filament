<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutocompleted
{
    protected $autocomplete;

    public function disableAutocomplete()
    {
        $this->configure(function () {
            $this->autocomplete('off');
        });

        return $this;
    }

    public function autocomplete($autocomplete = 'on')
    {
        $this->configure(function () use ($autocomplete) {
            $this->autocomplete = $autocomplete;
        });

        return $this;
    }

    public function getAutocomplete()
    {
        return $this->autocomplete;
    }
}
