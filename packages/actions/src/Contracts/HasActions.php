<?php

namespace Filament\Actions\Contracts;

use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;

interface HasActions extends HasForms
{
    /**
     * @param  string | array<string>  $name
     */
    public function getAction(string | array $name): ?Action;
}
