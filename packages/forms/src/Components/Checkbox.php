<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAutofocused;

    protected function setup()
    {
        $this->default(false);
    }
}
