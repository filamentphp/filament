<?php

namespace Filament\Tables\Columns\Contracts;

use Filament\Tables\Actions\Action;

interface HasExtraItemActions
{
    /**
     * @return array<Action>
     */
    public function getExtraItemActions(): array;
}
