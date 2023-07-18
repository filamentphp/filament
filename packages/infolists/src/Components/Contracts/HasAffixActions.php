<?php

namespace Filament\Infolists\Components\Contracts;

use Filament\Infolists\Components\Actions\Action;

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
