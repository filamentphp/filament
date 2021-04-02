<?php

namespace Filament\Forms\Components;

class Radio extends Field
{
    use Concerns\CanBeAutofocused;

    protected $options = [];

    public function getOptions()
    {
        return $this->options;
    }

    public function options($options)
    {
        $this->configure(function () use ($options) {
            $this->options = $options;
        });

        return $this;
    }
}
