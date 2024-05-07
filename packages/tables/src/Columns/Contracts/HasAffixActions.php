<?php

namespace Filament\Tables\Columns\Contracts;

use Filament\Tables\Actions\Action;

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
