<?php

namespace Filament\Forms\Components;

use Filament\Support\Concerns\HasExtraAlpineAttributes;

class TableBuilder extends Field
{
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.table-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([[null]]);
    }
}
