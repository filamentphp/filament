<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanGeneratePanels;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;

class MakePanelCommand extends Command
{
    use CanManipulateFiles;
    use CanGeneratePanels;

    protected $description = 'Create a new Filament panel';

    protected $signature = 'make:filament-panel {id?} {--F|force}';

    public function handle(): int
    {
        if (! $this->generatePanel(id: $this->argument('id'), placeholder: 'app', force: $this->option('force'))) {
            return static::FAILURE;
        }

        return static::SUCCESS;
    }
}
