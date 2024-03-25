<?php

namespace Filament\Forms\Components\Contracts;

use Filament\Actions\Action;

interface HasFooterActions
{
    /**
     * @return array<Action>
     */
    public function getFooterActions(): array;
}
