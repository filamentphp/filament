<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeColumnCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament column class and cell view.';

    protected $signature = 'make:filament-column {name} {--R|resource}';

    public function handle()
    {
        $column = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $columnClass = (string) Str::of($column)->afterLast('\\');
        $columnNamespace = Str::of($column)->contains('\\') ?
            (string) Str::of($column)->beforeLast('\\') :
            '';

        $view = Str::of($column)
            ->prepend('filament\\tables\\cells\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($column)
                ->prepend($this->option('resource') ? 'Filament\\Resources\\Tables\\Columns\\' : 'Filament\\Tables\\Columns\\')
                ->replace('\\', '/')
                ->append('.php'),
        );
        $viewPath = resource_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if ($this->checkForCollision([
            $path,
        ])) return;

        if (! $this->option('resource')) {
            $this->copyStubToApp('Column', $path, [
                'class' => $columnClass,
                'namespace' => 'App\\Filament\\Tables\\Columns' . ($columnNamespace !== '' ? "\\{$columnNamespace}" : ''),
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp('ResourceColumn', $path, [
                'class' => $columnClass,
                'namespace' => 'App\\Filament\\Resources\\Tables\\Columns' . ($columnNamespace !== '' ? "\\{$columnNamespace}" : ''),
                'view' => $view,
            ]);
        }

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('CellView', $viewPath);
        }

        $this->info("Successfully created {$column}!");
    }
}
