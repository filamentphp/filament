<?php

namespace Filament\Tables\Columns;

class Boolean extends Icon
{
    protected function setUp()
    {
        $this->options([
            'heroicon-s-check-circle' => fn ($value) => $value,
            'heroicon-o-x-circle' => fn ($value) => !$value,
        ]);
    }
}
