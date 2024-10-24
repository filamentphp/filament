<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:about')]
class AboutCommand extends Command
{
    protected $signature = 'filament:about';

    protected $description = 'Display basic information about Filament packages that are installed';

    public function handle(): void
    {
        $this->call('about', [
            '--only' => 'filament',
        ]);
    }
}
