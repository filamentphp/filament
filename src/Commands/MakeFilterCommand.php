<?php

namespace Filament\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MakeFilterCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Make a Filament filter class.';

    protected $signature = 'make:filament-filter {name} {--R|resource=}';

    public function handle()
    {
        $filter = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $filterClass = (string) Str::of($filter)->afterLast('\\');
        $filterNamespace = Str::of($filter)->contains('\\') ?
            (string) Str::of($filter)->beforeLast('\\') :
            '';

        $resource = $this->option('resource');

        $path = app_path(
            (string) Str::of($filter)
                ->prepend('Filament\\Filters\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if ($this->checkForCollision([
            $path,
        ])) return;

        if ($resource === null) {
            $this->copyStubToApp('Filter', $path, [
                'class' => $filterClass,
                'namespace' => 'App\\Filament\\Filters',
            ]);
        } else {
            $this->copyStubToApp('ResourceFilter', $path, [
                'class' => $filterClass,
                'namespace' => 'App\\Filament\\Filters',
            ]);
        }

        $this->info("Successfully created {$filter}!");
    }
}
