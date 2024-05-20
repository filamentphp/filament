<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:clear-cached-components')]
class ClearCachedComponentsCommand extends Command
{
    protected $description = 'Clear all cached components';

    protected $signature = 'filament:clear-cached-components';

    public function handle(): int
    {
        $this->info('Clearing cached components...');

        foreach (Filament::getPanels() as $panel) {
            $panel->clearCachedComponents();
        }

        $this->info('All done!');

        return static::SUCCESS;
    }
}
