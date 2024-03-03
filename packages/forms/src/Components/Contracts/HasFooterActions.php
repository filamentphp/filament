<?php

namespace Filament\Forms\Components\Contracts;

use Filament\Forms\Components\Actions\Action;

interface HasFooterActions
{
    /**
     * @return array<Action>
     */
    public function getFooterActions(): array;
}
