<?php

namespace Filament\Support\View\Components;

use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class CalloutComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        return [];
    }
}
