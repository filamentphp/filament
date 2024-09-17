<?php

namespace Filament\Widgets\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-widget')]
class MakeWidgetCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new Filament widget class';

    protected $signature = 'make:filament-widget {name?} {--R|resource=} {--C|chart} {--T|table} {--S|stats-overview} {--panel=} {--F|force}';

    public function handle(): int
    {
        $widget = (string) str($this->argument('name') ?? text(
            label: 'What is the widget name?',
            placeholder: 'BlogPostsChart',
            required: true,
        ))
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

        $type = match (true) {
            $this->option('chart') => 'Chart',
            $this->option('stats-overview') => 'Stats overview',
            $this->option('table') => 'Table',
            default => select(
                label: 'What type of widget do you want to create?',
                options: ['Chart', 'Stats overview', 'Table', 'Custom'],
            ),
        };

        if (class_exists(Resource::class)) {
            $resourceInput = $this->option('resource') ?? text(
                label: 'What is the resource you would like to create this in?',
                placeholder: '[Optional] BlogPostResource',
            );

            if (filled($resourceInput)) {
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
                $panel = Filament::getPanel($panel, isStrict: false);
            }

            if (! $panel) {
                $panels = Filament::getPanels();
                $namespace = config('livewire.class_namespace');

                /** @var ?Panel $panel */
                $panel = $panels[select(
                    label: 'Where would you like to create this?',
                    options: array_unique([
                        ...array_map(
                            fn (Panel $panel): string => "The [{$panel->getId()}] panel",
                            $panels,
                        ),
                        $namespace => "[{$namespace}] alongside other Livewire components",
                    ])
                )] ?? null;
            }
        }

        $path = null;
        $namespace = null;
        $resourcePath = null;
        $resourceNamespace = null;

        if (! $panel) {
            $namespace = config('livewire.class_namespace');
            $path = app_path((string) str($namespace)->after('App\\')->replace('\\', '/'));
        } elseif ($resource === null) {
            $widgetDirectories = $panel->getWidgetDirectories();
            $widgetNamespaces = $panel->getWidgetNamespaces();

            foreach ($widgetDirectories as $widgetIndex => $widgetDirectory) {
                if (str($widgetDirectory)->startsWith(base_path('vendor'))) {
                    unset($widgetDirectories[$widgetIndex]);
                    unset($widgetNamespaces[$widgetIndex]);
                }
            }

            $namespace = (count($widgetNamespaces) > 1) ?
                select(
                    label: 'Which namespace would you like to create this in?',
                    options: $widgetNamespaces,
                ) :
                (Arr::first($widgetNamespaces) ?? 'App\\Filament\\Widgets');
            $path = (count($widgetDirectories) > 1) ?
                $widgetDirectories[array_search($namespace, $widgetNamespaces)] :
                (Arr::first($widgetDirectories) ?? app_path('Filament/Widgets/'));
        } else {
            $resourceDirectories = $panel->getResourceDirectories();
            $resourceNamespaces = $panel->getResourceNamespaces();

            foreach ($resourceDirectories as $resourceIndex => $resourceDirectory) {
                if (str($resourceDirectory)->startsWith(base_path('vendor'))) {
                    unset($resourceDirectories[$resourceIndex]);
                    unset($resourceNamespaces[$resourceIndex]);
                }
            }

            $resourceNamespace = (count($resourceNamespaces) > 1) ?
                select(
                    label: 'Which namespace would you like to create this in?',
                    options: $resourceNamespaces,
                ) :
                (Arr::first($resourceNamespaces) ?? 'App\\Filament\\Resources');
            $resourcePath = (count($resourceDirectories) > 1) ?
                $resourceDirectories[array_search($resourceNamespace, $resourceNamespaces)] :
                (Arr::first($resourceDirectories) ?? app_path('Filament/Resources/'));
        }

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
            ...($this->option('stats-overview') || $this->option('chart')) ? [] : [$viewPath],
        ])) {
            return static::INVALID;
        }

        if ($type === 'Chart') {
            $chartType = select(
                label: 'Which type of chart would you like to create?',
                options: [
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
                'type' => match ($chartType) {
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
        } elseif ($type === 'Stats overview') {
            $this->copyStubToApp('StatsOverviewWidget', $path, [
                'class' => $widgetClass,
                'namespace' => filled($resource) ? "{$resourceNamespace}\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : $namespace . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : ''),
            ]);
        } elseif ($type === 'Table') {
            $this->copyStubToApp('TableWidget', $path, [
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

        $this->components->info("Filament widget [{$path}] created successfully.");

        if ($resource !== null) {
            $this->components->info("Make sure to register the widget in `{$resourceClass}::getWidgets()`, and then again in `getHeaderWidgets()` or `getFooterWidgets()` of any `{$resourceClass}` page.");
        }

        return static::SUCCESS;
    }
}
