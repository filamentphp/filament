<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;

class CacheComponentsCommand extends Command
{
    protected $description = 'Cache all components';

    protected $signature = 'filament:cache-components';

    public function handle(): int
    {
        $this->info('Caching registered components...');

        foreach (Filament::getPanels() as $panel) {
            $panel->cacheComponents();
        }

        $this->info('All done!');

        return static::SUCCESS;
    }
}
