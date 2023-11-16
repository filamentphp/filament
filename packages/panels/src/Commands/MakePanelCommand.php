<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanGeneratePanels;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use ReflectionClass;

class MakePanelCommand extends Command
{
    use CanGeneratePanels;
    use CanManipulateFiles;

    protected $description = 'Create a new Filament panel';

    protected $signature = 'make:filament-panel {id?} {--F|force}';

    public function handle(): int
    {
        if (! $this->generatePanel(id: $this->argument('id'), placeholder: 'app', force: $this->option('force'))) {
            return static::FAILURE;
        }

        return static::SUCCESS;
    }

    /**
     * We need to override this method as the panel provider
     * stubs are part of the support package, not panels.
     */
    protected function getDefaultStubPath(): string
    {
        $reflectionClass = new ReflectionClass($this);

        return (string) str($reflectionClass->getFileName())
            ->beforeLast('Commands')
            ->append('../../support/stubs');
    }
}
