<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;

class UpgradeCommand extends Command
{
    protected $description = 'Upgrade Filament to the latest version.';

    protected $signature = 'filament:upgrade';

    public function handle(): int
    {
        foreach ([
            AssetsCommand::class,
            'config:clear',
            'livewire:discover',
            'route:clear',
            'view:clear',
        ] as $command) {
            $this->call($command);
        }

        $this->components->info('Successfully upgraded!');

        return static::SUCCESS;
    }
}
