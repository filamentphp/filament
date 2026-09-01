<?php

namespace Filament\Schemas\Components\Contracts;

use Filament\Actions\Action;

interface HasAffixActions
{
    /**
     * @return array<Action>
     */
    public function getPrefixActions(): array;

    /**
     * @return array<Action>
     */
    public function getSuffixActions(): array;
}
