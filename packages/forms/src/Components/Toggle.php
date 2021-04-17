<?php

namespace Filament\Forms\Components;

use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Field;

class Toggle extends Field
{
    use Concerns\CanBeAutofocused;

    protected function setUp()
    {
        $this->default(false);
    }
}
