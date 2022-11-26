<?php

namespace Filament\Forms\Components;

class Hidden extends Field
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.hidden';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('hidden');
    }
}
