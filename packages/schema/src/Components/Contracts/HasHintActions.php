<?php

namespace Filament\Schema\Components\Contracts;

use Filament\Actions\Action;

interface HasHintActions
{
    /**
     * @return array<Action>
     */
    public function getHintActions(): array;
}
