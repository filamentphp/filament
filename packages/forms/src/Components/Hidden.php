<?php

namespace Filament\Forms\Components;

class Hidden extends Field
{
    protected string $view = 'forms::components.hidden';

    protected function setUp(): void
    {
        $this->columnSpan('hidden');
    }
}
