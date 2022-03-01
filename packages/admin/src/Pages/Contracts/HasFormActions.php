<?php

namespace Filament\Pages\Contracts;

use Filament\Pages\Actions\Action;

interface HasFormActions
{
    public function getCachedFormAction(string $name): ?Action;
}
