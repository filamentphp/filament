<?php

namespace Filament\Schema\Components\Contracts;

use Filament\Actions\Action;

interface HasExtraItemActions
{
    /**
     * @return array<Action>
     */
    public function getExtraItemActions(): array;
}
