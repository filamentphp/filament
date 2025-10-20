<?php

namespace Filament\Support\Commands;

use Filament\Support\Events\FilamentUpgraded;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:upgrade')]
class UpgradeCommand extends Command
{
    protected $description = 'Upgrade Filament to the latest version';

    protected $name = 'filament:upgrade';

    public function handle(): int
    {
        foreach ([
            AssetsCommand::class,
            'config:clear',
            'route:clear',
            'view:clear',
        ] as $command) {
            $this->call($command);
        }

        FilamentUpgraded::dispatch();

        $this->components->info('Successfully upgraded!');

        return static::SUCCESS;
    }
}
