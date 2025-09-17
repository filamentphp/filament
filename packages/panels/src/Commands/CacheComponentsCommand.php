<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:cache-components')]
class CacheComponentsCommand extends Command
{
    protected $description = 'Cache all components';

    protected $name = 'filament:cache-components';

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
