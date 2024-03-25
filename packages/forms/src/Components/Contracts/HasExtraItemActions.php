<?php

namespace Filament\Forms\Components\Contracts;

use Filament\Actions\Action;

interface HasExtraItemActions
{
    /**
     * @return array<Action>
     */
    public function getExtraItemActions(): array;
}
