<?php

namespace Filament\Support\Commands;

use Filament\Support\Commands\Concerns\CanCachePanelComponents;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:optimize')]
class OptimizeCommand extends Command
{
    use CanCachePanelComponents;

    protected $signature = 'filament:optimize';

    protected $description = 'Cache components and Blade icons to improve performance.';

    public function handle(): int
    {
        $this->components->info('Caching components and Blade icons.');

        $tasks = collect([
            'Caching Blade icons' => fn (): bool => $this->callSilent('icons:cache') === static::SUCCESS,
        ]);

        if ($this->canCachePanelComponents()) {
            $tasks->put(
                'Caching components',
                fn (): bool => $this->callSilent('filament:cache-components') === static::SUCCESS
            );
        }

        $tasks->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
