<?php

namespace Filament\Commands;

use Illuminate\Console\Command;

class MakeThemeCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Create a Filament CSS theme.';

    protected $signature = 'make:filament-theme {name}';

    public function handle()
    {
        $path = resource_path("css/filament/{$this->argument('name')}.css");

        if ($this->checkForCollision([
            $path,
        ])) return;

        $this->copyStubToApp('Theme', $path);

        $this->info("Successfully created {$this->argument('name')} theme!");
    }
}
