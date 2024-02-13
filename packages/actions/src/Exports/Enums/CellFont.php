<?php

namespace Filament\Actions\Exports\Enums;

use Spatie\Color\Rgb;

enum CellFont: string
{
    case Bold = 'bold';

    case Italic = 'italic';

    case Underline = 'underline';

    case Strikethrough = 'strikethrough';

    public static function size(int $value): int
    {
        return $value;
    }

    public static function family(string $value): string
    {
        return ucfirst($value);
    }

    public static function color(string $value): Rgb
    {
        return Rgb::fromString($value);
    }
}
