<?php

namespace Filament\Support\Commands;

use Closure;
use Filament\Support\Commands\Concerns\CanCachePanelComponents;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:optimize')]
class OptimizeCommand extends Command
{
    use CanCachePanelComponents;

    protected $signature = 'filament:optimize';

    protected $description = 'Cache components and Blade icons to increase performance';

    public function handle(): int
    {
        $this->components->info('Caching components and Blade icons.');

        $tasks = collect();

        if ($this->canCachePanelComponents()) {
            $tasks->put(
                'Caching components',
                fn (): bool => $this->callSilent('filament:cache-components') === static::SUCCESS
            );
        }

        $tasks->put('Caching Blade icons', fn (): bool => $this->callSilent('icons:cache') === static::SUCCESS);

        $tasks->each(fn (Closure $task, string $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
