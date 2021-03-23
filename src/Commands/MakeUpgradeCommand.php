<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeUpgradeCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Upgrade Filament to the latest version.';

    protected $signature = 'make:filament-upgrade';

    public function handle()
    {
        system("composer update");

        system("php artisan migrate");

        system("php artisan livewire:discover");

        system("php artisan route:clear");

        system("php artisan view:clear");

        $this->info("Successfully upgraded!");
    }
}
