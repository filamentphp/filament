<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeThemeCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Create a Filament CSS theme.';

    protected $signature = 'make:filament-theme {name}';

    public function handle()
    {
        $theme = (string) Str::of($this->argument('name'))
            ->trim(' ');

        $path = resource_path("css/filament/{$theme}.css");

        if ($this->checkForCollision([
            $path,
        ])) return;

        $this->copyStubToApp('Theme', $path);

        $this->info("Successfully created {$theme} theme!");
    }
}
