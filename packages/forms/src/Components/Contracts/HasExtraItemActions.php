<?php

namespace Filament\Forms\Components\Contracts;

use Filament\Forms\Components\Actions\Action;

interface HasExtraItemActions
{
    /**
     * @return array<Action>
     */
    public function getExtraItemActions(): array;
}
