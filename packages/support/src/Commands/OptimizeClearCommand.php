<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:optimize-clear')]
class OptimizeClearCommand extends Command
{
    protected $signature = 'filament:optimize-clear';

    protected $description = 'Remove cached components and blade icons.';

    public function handle(): int
    {
        $this->components->info('Clearing cached components and blade icons.');

        collect([
            'Clearing cached Components' => fn () => $this->callSilent('filament:clear-cached-components') == 0,
            'Clearing cached Blade Icons' => fn () => $this->callSilent('icons:clear') == 0,
        ])->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
