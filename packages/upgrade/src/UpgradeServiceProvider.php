<?php

namespace Filament\Upgrade;

use Filament\Upgrade\Commands\UpgradeDirectoryStructureToV4Command;
use Illuminate\Support\ServiceProvider;

class UpgradeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            UpgradeDirectoryStructureToV4Command::class,
        ]);
    }
}
