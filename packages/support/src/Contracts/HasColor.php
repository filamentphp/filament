<?php

namespace Filament\Support\Contracts;

interface HasColor
{
    /**
     * @return string | array<string> | null
     */
    public function getColor(): string | array | null;
}
