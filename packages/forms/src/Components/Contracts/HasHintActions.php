<?php

namespace Filament\Forms\Components\Contracts;

use Filament\Forms\Components\Actions\Action;

interface HasHintActions
{
    /**
     * @return array<Action>
     */
    public function getHintActions(): array;
}
