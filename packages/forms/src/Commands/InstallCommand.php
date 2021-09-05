<?php

namespace Filament\Forms\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'forms:install';

    protected $description = 'Set up form builder CSS and JS in a fresh Laravel installation.';

    public function __invoke(): int
    {
        $this->call('ui', [
            'type' => 'forms',
        ]);

        return static::SUCCESS;
    }
}
