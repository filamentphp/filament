<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFieldCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament field class and view.';

    protected $signature = 'make:filament-field {name} {--R|resource}';

    public function handle()
    {
        $field = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $fieldClass = (string) Str::of($field)->afterLast('\\');
        $fieldNamespace = Str::of($field)->contains('\\') ?
            (string) Str::of($field)->beforeLast('\\') :
            '';

        $view = Str::of($field)
            ->prepend('filament\\forms\\components\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($field)
                ->prepend($this->option('resource') ? 'Filament\\Resources\\Forms\\Components\\' : 'Filament\\Forms\\Components\\')
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
            $this->copyStubToApp('Field', $path, [
                'class' => $fieldClass,
                'namespace' => 'App\\Filament\\Forms\\Components' . ($fieldNamespace !== '' ? "\\{$fieldNamespace}" : ''),
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp('ResourceField', $path, [
                'class' => $fieldClass,
                'namespace' => 'App\\Filament\\Resources\\Forms\\Components' . ($fieldNamespace !== '' ? "\\{$fieldNamespace}" : ''),
                'view' => $view,
            ]);
        }

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('FieldView', $viewPath);
        }

        $this->info("Successfully created {$field}!");
    }
}
