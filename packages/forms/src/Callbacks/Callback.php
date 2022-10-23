<?php

namespace Filament\Forms\Callbacks;

use Filament\Forms\Components\Component;

abstract class Callback
{
    public function __construct(
        protected Component $component,
    ) {
    }
}
