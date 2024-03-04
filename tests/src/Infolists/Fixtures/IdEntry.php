<?php

namespace Filament\Tests\Infolists\Fixtures;

use Filament\Infolists\Components\TextEntry;

class IdEntry extends TextEntry
{
    public static function getDefaultName(): ?string
    {
        return 'ID';
    }
}
