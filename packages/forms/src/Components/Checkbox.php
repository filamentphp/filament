<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAutofocused;

    protected function setUp()
    {
        $this->default(false);
    }
}
