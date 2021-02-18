<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRoleCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament role class.';

    protected $signature = 'make:filament-role {name}';

    public function handle()
    {
        $role = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $roleClass = (string) Str::of($role)->afterLast('\\');
        $roleNamespace = Str::of($role)->contains('\\') ?
            (string) Str::of($role)->beforeLast('\\') :
            '';

        $path = app_path(
            (string) Str::of($role)
                ->prepend('Filament\\Roles\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if ($this->checkForCollision([
            $path,
        ])) return;

        $this->copyStubToApp('Role', $path, [
            'class' => $roleClass,
            'namespace' => 'App\Filament\Roles' . ($roleNamespace !== '' ? "\\{$roleNamespace}" : ''),
        ]);

        $this->info("Successfully created {$role}!");
    }
}
