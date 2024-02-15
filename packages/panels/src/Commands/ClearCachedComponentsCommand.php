<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;

class ClearCachedComponentsCommand extends Command
{
    protected $description = 'Cache all components';

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
