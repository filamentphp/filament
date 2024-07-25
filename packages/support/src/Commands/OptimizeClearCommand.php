<?php

namespace Filament\Support\Commands;

use Closure;
use Filament\Support\Commands\Concerns\CanCachePanelComponents;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:optimize-clear')]
class OptimizeClearCommand extends Command
{
    use CanCachePanelComponents;

    protected $signature = 'filament:optimize-clear';

    protected $description = 'Remove the cached components and Blade icons';

    public function handle(): int
    {
        $this->components->info('Clearing cached components and Blade icons.');

        $tasks = collect();

        if ($this->canCachePanelComponents()) {
            $tasks->put(
                'Clearing cached components',
                fn (): bool => $this->callSilent('filament:clear-cached-components') === static::SUCCESS
            );
        }

        $tasks->put(
            'Clearing cached Blade icons',
            fn (): bool => $this->callSilent('icons:clear') === static::SUCCESS
        );

        $tasks->each(fn (Closure $task, string $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
