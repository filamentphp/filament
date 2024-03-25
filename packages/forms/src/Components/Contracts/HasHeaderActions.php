<?php

namespace Filament\Forms\Components\Contracts;

use Filament\Actions\Action;

interface HasHeaderActions
{
    /**
     * @return array<Action>
     */
    public function getHeaderActions(): array;
}
