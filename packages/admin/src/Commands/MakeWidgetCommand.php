<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament widget class.';

    protected $signature = 'make:filament-widget {name?} {--R|resource=} {--F|force}';

    public function handle(): int
    {
        $widget = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `BlogPostsChart`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $widgetClass = (string) Str::of($widget)->afterLast('\\');
        $widgetNamespace = Str::of($widget)->contains('\\') ?
            (string) Str::of($widget)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;

        $resourceInput = $this->option('resource') ?? $this->ask('(Optional) Resource (e.g. `BlogPostResource`)');

        if ($resourceInput !== null) {
            $resource = (string) Str::of($resourceInput)
                ->studly()
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (! Str::of($resource)->endsWith('Resource')) {
                $resource .= 'Resource';
            }

            $resourceClass = (string) Str::of($resource)
                ->afterLast('\\');
        }

        $view = Str::of($widget)
            ->prepend($resource === null ? 'filament\\widgets\\' : "filament\\resources\\{$resource}\\widgets\\")
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($widget)
                ->prepend($resource === null ? 'Filament\\Widgets\\' : "Filament\\Resources\\{$resource}\\Widgets\\")
                ->replace('\\', '/')
                ->append('.php'),
        );
        $viewPath = resource_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
            $viewPath,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('Widget', $path, [
            'class' => $widgetClass,
            'namespace' => filled($resource) ? "App\\Filament\\Resources\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : 'App\\Filament\\Widgets' . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : ''),
            'view' => $view,
        ]);

        $this->copyStubToApp('WidgetView', $viewPath);

        $this->info("Successfully created {$widget}!");

        if ($resource !== null) {
            $this->info("Make sure to register the widget in `{$resourceClass}::getWidgets()`, and then again in `getHeaderWidgets()` or `getFooterWidgets()` of any `{$resourceClass}` page.");
        }

        return static::SUCCESS;
    }
}
