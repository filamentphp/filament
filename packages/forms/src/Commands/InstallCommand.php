<?php

namespace Filament\Forms\Commands;

use Filament\Forms\FormsPreset;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'forms:install';

    protected $description = 'Set up form builder CSS and JS in a fresh Laravel installation.';

    public function __invoke(): int
    {
        FormsPreset::install();

        $this->info('Scaffolding installed successfully.');

        $this->comment('Please run "npm install && npm run dev" to compile your new assets.');

        return static::SUCCESS;
    }
}
