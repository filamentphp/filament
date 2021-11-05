<?php

namespace Filament\Tables\Commands;

use Filament\Tables\TablesPreset;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'tables:install';

    protected $description = 'Set up table builder CSS and JS in a fresh Laravel installation.';

    public function __invoke(): int
    {
        TablesPreset::install();

        $this->info('Scaffolding installed successfully.');

        $this->comment('Please run "npm install && npm run dev" to compile your new assets.');

        return static::SUCCESS;
    }
}
