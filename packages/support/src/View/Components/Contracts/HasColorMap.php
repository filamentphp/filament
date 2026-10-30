<?php

namespace Filament\Support\View\Components\Contracts;

interface HasColorMap
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array;
}
