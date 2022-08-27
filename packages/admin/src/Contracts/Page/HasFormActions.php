<?php

declare(strict_types=1);

namespace Filament\Contracts\Page;

use Filament\Pages\Actions\Action;

interface HasFormActions
{
    /**
     * Returns the cached form actions.
     *
     * @param string $name
     * @return Action|null
     */
    public function getCachedFormAction(string $name): ?Action;
}
