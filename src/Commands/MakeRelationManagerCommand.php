<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRelationManagerCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament relation manager class for a resource.';

    protected $signature = 'make:filament-relation-manager {resource} {relationship}';

    public function handle()
    {
        $resource = (string) Str::of($this->argument('resource'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $relationship = (string) Str::of($this->argument('relationship'))->trim(' ');
        $managerClass = (string) Str::of($relationship)
            ->studly()
            ->append('RelationManager');

        $path = app_path(
            (string) Str::of($managerClass)
                ->prepend("Filament\\Resources\\{$resource}\\RelationManagers\\")
                ->replace('\\', '/')
                ->append('.php'),
        );

        if ($this->checkForCollision([
            $path,
        ])) return;

        $this->copyStubToApp('RelationManager', $path, [
            'namespace' => "App\\Filament\\Resources\\{$resource}\\RelationManagers",
            'managerClass' => $managerClass,
            'relationship' => $relationship,
        ]);

        $this->info("Successfully created {$managerClass}!");
    }
}
