<?php

namespace Filament\Infolists\Components\Contracts;

use Filament\Infolists\Components\Actions\Action;

interface HasHeaderActions
{
    /**
     * @return array<Action>
     */
    public function getHeaderActions(): array;
}
