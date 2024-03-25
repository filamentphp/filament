<?php

namespace Filament\Schema\Components\Contracts;

use Filament\Actions\Action;

interface HasFooterActions
{
    /**
     * @return array<Action>
     */
    public function getFooterActions(): array;
}
