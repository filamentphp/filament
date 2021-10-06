<?php

namespace Filament\Tables\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'tables:install';

    protected $description = 'Set up table builder CSS and JS in a fresh Laravel installation.';

    public function __invoke(): int
    {
        $this->call('ui', [
            'type' => 'tables',
        ]);

        return static::SUCCESS;
    }
}
