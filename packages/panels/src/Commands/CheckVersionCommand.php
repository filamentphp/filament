<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:version', aliases: [
    'filament:about',
    'filament:v',
])]
class CheckVersionCommand extends Command
{
    protected $description = 'Check the Filament version';

    protected $signature = 'filament:version';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:about',
        'filament:v',
    ];

    public function handle(): int
    {
        $this->call('about', ['--only' => 'Filament']);

        return static::SUCCESS;
    }
}
