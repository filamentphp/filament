<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:optimize')]
class OptimizeCommand extends Command
{
    protected $signature = 'filament:optimize';

    protected $description = 'Optimize Filament.';

    public function handle(): int
    {
        $this->components->info('Caching components and blade icons to increase performance.');

        collect([
            'Caching components' => fn () => $this->callSilent('filament:cache-components') == 0,
            'Caching blade icons' => fn () => $this->callSilent('icons:cache') == 0,
        ])->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
