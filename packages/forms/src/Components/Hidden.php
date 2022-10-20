<?php

namespace Filament\Forms\Components;

class Hidden extends Field
{
    protected string $view = 'filament-forms::components.hidden';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('hidden');
    }
}
