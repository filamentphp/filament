<?php

namespace Filament\Actions\Contracts;

use Filament\Actions\Action;

interface HasActions
{
    /**
     * @param  string | array<string>  $name
     */
    public function getAction(string | array $name): ?Action;
}
