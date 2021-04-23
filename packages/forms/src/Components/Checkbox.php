<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeInline;

    protected function setUp()
    {
        $this->default(false);

        $this->inline();
    }
}
