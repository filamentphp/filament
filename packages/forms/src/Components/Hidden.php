<?php

namespace Filament\Forms\Components;

class Hidden extends Field
{
    use Concerns\CanBeAutocompleted;

    protected string $view = 'forms::components.hidden';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('hidden');
    }
}
