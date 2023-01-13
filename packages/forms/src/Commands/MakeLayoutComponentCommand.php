<?php

namespace Filament\Forms\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeLayoutComponentCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $description = 'Creates a form layout component class and view.';

    protected $signature = 'make:form-layout {name?} {--F|force}';

    public function handle(): int
    {
        $component = (string) str($this->argument('name') ?? $this->askRequired('Name (e.g. `Wizard`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) str($component)->afterLast('\\');
        $componentNamespace = str($component)->contains('\\') ?
            (string) str($component)->beforeLast('\\') :
            '';

        $view = str($component)
            ->prepend('forms\\components\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) str($component)
                ->prepend('Forms\\Components\\')
                ->replace('\\', '/')
                ->append('.php'),
        );
        $viewPath = resource_path(
            (string) str($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('LayoutComponent', $path, [
            'class' => $componentClass,
            'namespace' => 'App\\Forms\\Components' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
            'view' => $view,
        ]);

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('LayoutComponentView', $viewPath);
        }

        $this->components->info("Successfully created {$component}!");

        return static::SUCCESS;
    }
}
