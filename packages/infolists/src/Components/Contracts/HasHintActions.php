<?php

namespace Filament\Infolists\Components\Contracts;

use Filament\Infolists\Components\Actions\Action;

interface HasHintActions
{
    /**
     * @return array<Action>
     */
    public function getHintActions(): array;
}
