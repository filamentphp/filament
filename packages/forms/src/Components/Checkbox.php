<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanAlternateLayoutDirection;

    protected function setUp()
    {
        $this->default(false)->inline();
    }
}
