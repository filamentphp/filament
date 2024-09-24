<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor as ColorInterface;

enum ColorEnum implements ColorInterface
{
    case Red;

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Red => Color::Red,
        };
    }
}
