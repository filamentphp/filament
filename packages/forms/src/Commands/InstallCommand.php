<?php

namespace Filament\Forms\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'forms:install';

    public function __invoke(): int
    {
        $this->call('ui forms');

        return static::SUCCESS;
    }
}
