<?php

namespace Filament\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;

class IdField extends TextInput
{
    public static function getDefaultName(): ?string
    {
        return 'ID';
    }
}
