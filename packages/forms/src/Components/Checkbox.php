<?php

namespace Filament\Forms\Components;

class Checkbox extends Field
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;

    protected string $view = 'forms::components.checkbox';

    protected function setUp(): void
    {
        $this->default(false);

        $this->inline();
    }
}
