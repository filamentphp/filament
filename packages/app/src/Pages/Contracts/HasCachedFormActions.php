<?php

namespace Filament\Pages\Contracts;

use Filament\Pages\Actions\Action;

interface HasCachedFormActions
{
    public function getCachedFormAction(string $name): ?Action;
}
