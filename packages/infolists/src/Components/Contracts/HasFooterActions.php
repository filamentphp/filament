<?php

namespace Filament\Infolists\Components\Contracts;

use Filament\Infolists\Components\Actions\Action;

interface HasFooterActions
{
    /**
     * @return array<Action>
     */
    public function getFooterActions(): array;
}
