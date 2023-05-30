<?php

namespace Filament\Widgets\Commands;

use Filament\Panel;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $description = 'Creates a Filament widget class.';

    protected $signature = 'make:filament-widget {name?} {--R|resource=} {--C|chart} {--T|table} {--S|stats-overview} {--panel=} {--F|force}';

    public function handle(): int
    {
        $widget = (string) str($this->argument('name') ?? $this->askRequired('Name (e.g. `BlogPostsChart`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $widgetClass = (string) str($widget)->afterLast('\\');
        $widgetNamespace = str($widget)->contains('\\') ?
            (string) str($widget)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;

        if (class_exists(Resource::class)) {
            $resourceInput = $this->option('resource') ?? $this->ask('(Optional) Resource (e.g. `BlogPostResource`)');

            if ($resourceInput !== null) {
                $resource = (string) str($resourceInput)
                    ->studly()
                    ->trim('/')
                    ->trim('\\')
                    ->trim(' ')
                    ->replace('/', '\\');

                if (! str($resource)->endsWith('Resource')) {
                    $resource .= 'Resource';
                }

                $resourceClass = (string) str($resource)
                    ->afterLast('\\');
            }
        }

        $panel = null;

        if (class_exists(Panel::class)) {
            $panel = $this->option('panel');

            if ($panel) {
                $panel = Filament::getPanel($panel);
            }

            if (! $panel) {
                $panels = Filament::getPanels();

                /** @var ?Panel $panel */
                $panel = $panels[$this->choice(
                    'Where would you like to create this widget?',
                    array_unique([
                        ...array_map(
                            fn (Panel $panel): string => $panel->getWidgetNamespace() ?? 'App\\Filament\\Widgets',
                            $panels,
                        ),
                        '' => 'App\\Http\\Livewire' . ($widgetNamespace ? '\\' . $widgetNamespace : ''),
                    ]),
                )] ?? null;
            }
        }

        $path = $panel ? ($panel->getWidgetDirectory() ?? app_path('Filament/Widgets/')) : app_path('Http/Livewire/');
        $namespace = $panel ? ($panel->getWidgetNamespace() ?? 'App\\Filament\\Widgets') : 'App\\Http\\Livewire';
        $resourcePath = $panel ? ($panel->getResourceDirectory() ?? app_path('Filament/Resources/')) : null;
        $resourceNamespace = $panel ? ($panel->getResourceNamespace() ?? 'App\\Filament\\Resources') : null;

        $view = str($widget)->prepend(
            (string) str($resource === null ? ($panel ? "{$namespace}\\" : 'livewire\\') : "{$resourceNamespace}\\{$resource}\\widgets\\")
                ->replaceFirst('App\\', '')
        )
            ->replace('\\', '/')
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('.');

        $path = (string) str($widget)
            ->prepend('/')
            ->prepend($resource === null ? $path : "{$resourcePath}\\{$resource}\\Widgets\\")
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $viewPath = resource_path(
            (string) str($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
            ($this->option('stats-overview') || $this->option('chart')) ?: $viewPath,
        ])) {
            return static::INVALID;
        }

        if ($this->option('chart')) {
            $chart = $this->choice(
                'Chart type',
                [
                    'Bar chart',
                    'Bubble chart',
                    'Doughnut chart',
                    'Line chart',
                    'Pie chart',
                    'Polar area chart',
                    'Radar chart',
                    'Scatter chart',
                ],
            );

            $this->copyStubToApp('ChartWidget', $path, [
                'class' => $widgetClass,
                'namespace' => filled($resource) ? "{$resourceNamespace}\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : $namespace . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : ''),
                'type' => match ($chart) {
                    'Bar chart' => 'bar',
                    'Bubble chart' => 'bubble',
                    'Doughnut chart' => 'doughnut',
                    'Pie chart' => 'pie',
                    'Polar area chart' => 'polarArea',
                    'Radar chart' => 'radar',
                    'Scatter chart' => 'scatter',
                    default => 'line',
                },
            ]);
        } elseif ($this->option('table')) {
            $this->copyStubToApp('TableWidget', $path, [
                'class' => $widgetClass,
                'namespace' => filled($resource) ? "{$resourceNamespace}\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : $namespace . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : ''),
            ]);
        } elseif ($this->option('stats-overview')) {
            $this->copyStubToApp('StatsOverviewWidget', $path, [
                'class' => $widgetClass,
                'namespace' => filled($resource) ? "{$resourceNamespace}\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : $namespace . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : ''),
            ]);
        } else {
            $this->copyStubToApp('Widget', $path, [
                'class' => $widgetClass,
                'namespace' => filled($resource) ? "{$resourceNamespace}\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : $namespace . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : ''),
                'view' => $view,
            ]);

            $this->copyStubToApp('WidgetView', $viewPath);
        }

        $this->components->info("Successfully created {$widget}!");

        if ($resource !== null) {
            $this->components->info("Make sure to register the widget in `{$resourceClass}::getWidgets()`, and then again in `getHeaderWidgets()` or `getFooterWidgets()` of any `{$resourceClass}` page.");
        }

        return static::SUCCESS;
    }
}
