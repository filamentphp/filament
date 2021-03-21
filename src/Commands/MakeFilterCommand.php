<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFilterCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Make a Filament filter class.';

    protected $signature = 'make:filament-filter {name} {--R|resource}';

    public function handle()
    {
        $filter = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $filterClass = (string) Str::of($filter)->afterLast('\\');
        $filterName = (string) Str::of($filterClass)
            ->kebab()
            ->lower()
            ->beforeLast('-filter')
            ->camel();
        $filterNamespace = Str::of($filter)->contains('\\') ?
            (string) Str::of($filter)->beforeLast('\\') :
            '';

        $path = app_path(
            (string) Str::of($filter)
                ->prepend($this->option('resource') ? 'Filament\\Resources\\Tables\\Filters\\' : 'Filament\\Tables\\Filters\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if ($this->checkForCollision([
            $path,
        ])) {
            return;
        }

        if (! $this->option('resource')) {
            $this->copyStubToApp('Filter', $path, [
                'class' => $filterClass,
                'name' => $filterName,
                'namespace' => 'App\\Filament\\Tables\\Filters' . ($filterNamespace !== '' ? "\\{$filterNamespace}" : ''),
            ]);
        } else {
            $this->copyStubToApp('ResourceFilter', $path, [
                'class' => $filterClass,
                'name' => $filterName,
                'namespace' => 'App\\Filament\\Resources\\Tables\\Filters' . ($filterNamespace !== '' ? "\\{$filterNamespace}" : ''),
            ]);
        }

        $this->info("Successfully created {$filter}!");
    }
}
