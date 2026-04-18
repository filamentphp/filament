<?php

namespace Filament\Forms\Components\RichEditor\Plugins\Contracts;

interface HasToolbarButtons
{
    /**
     * @return array<string | array<string | array<string>>>
     */
    public function getEnabledToolbarButtons(): array;

    /**
     * @return array<string>
     */
    public function getDisabledToolbarButtons(): array;
}
