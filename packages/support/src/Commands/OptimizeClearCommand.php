<?php

namespace Filament\Support\Commands;

use Filament\Support\Concerns\CanCacheComponents;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:optimize-clear')]
class OptimizeClearCommand extends Command
{
    use CanCacheComponents;

    protected $signature = 'filament:optimize-clear';

    protected $description = 'Remove cached components and Blade icons.';

    public function handle(): int
    {
        $this->components->info('Clearing cached components and Blade icons.');

        $tasks = collect([
            'Clearing cached Blade icons' => fn (): bool => $this->callSilent('icons:clear') === static::SUCCESS,
        ]);

        if ($this->canCacheComponents()) {
            $tasks->put(
                'Clearing cached components',
                fn (): bool => $this->callSilent('filament:clear-cached-components') === static::SUCCESS
            );
        }

        $tasks->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
