<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRoleCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament role.';

    protected $signature = 'make:filament-role {name}';

    public function handle()
    {
        $class = (string) Str::of($this->argument('name'))
            ->trim()
            ->studly()
            ->append('Role');

        $path = app_path("Filament/Roles/{$class}.php");

        if ($this->checkForCollision([
            $path,
        ])) return;

        $this->copyStubToApp('Role', $path, [
            'class' => $class,
        ]);

        $this->info("Successfully created {$class}!");
    }
}
