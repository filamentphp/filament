<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:about')]
class AboutCommand extends Command
{
    protected $description = 'Display basic information about Filament packages that are installed';

    protected $name = 'filament:about';

    public function handle(): void
    {
        $this->call('about', [
            '--only' => 'filament',
        ]);
    }
}
