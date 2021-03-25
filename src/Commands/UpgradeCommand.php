<?php

namespace Filament\Commands;

use Illuminate\Console\Command;

class UpgradeCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Upgrade Filament to the latest version.';

    protected $signature = 'filament:upgrade';

    public function handle()
    {
        system('composer update');

        foreach ([
            'migrate',
            'livewire:discover',
            'route:clear',
            'view:clear',
        ] as $command) {
            $this->call($command);
        }

        $this->info('Successfully upgraded!');
    }
}
