<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanAlternateToColumnLayout;

    protected function setUp()
    {
        $this->default(false);
    }
}
