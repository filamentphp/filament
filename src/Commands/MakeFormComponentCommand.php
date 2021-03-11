<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFormComponentCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament form component class and view.';

    protected $signature = 'make:filament-form-component {name} {--R|resource}';

    public function handle()
    {
        $component = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) Str::of($component)->afterLast('\\');
        $componentNamespace = Str::of($component)->contains('\\') ?
            (string) Str::of($component)->beforeLast('\\') :
            '';

        $view = Str::of($component)
            ->prepend('filament\\forms\\components\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($component)
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
            $this->copyStubToApp('FormComponent', $path, [
                'class' => $componentClass,
                'namespace' => 'App\\Filament\\Forms\\Components' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp('ResourceFormComponent', $path, [
                'class' => $componentClass,
                'namespace' => 'App\\Filament\\Resources\\Forms\\Components' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
                'view' => $view,
            ]);
        }

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('FormComponentView', $viewPath);
        }

        $this->info("Successfully created {$component}!");
    }
}
