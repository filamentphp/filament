<?php

namespace Filament\Pages\Contracts;

use Filament\Actions\Action;

interface HasCachedFormActions
{
    public function getCachedFormAction(string $name): ?Action;
}
