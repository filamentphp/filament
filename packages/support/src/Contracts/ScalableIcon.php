<?php

namespace Filament\Support\Contracts;

use Filament\Support\Enums\IconSize;

interface ScalableIcon
{
    public function getIconForSize(IconSize $size): string;
}
