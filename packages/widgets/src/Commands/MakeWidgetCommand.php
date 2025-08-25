<?php

namespace Filament\Widgets\Commands;

use Filament\Support\Commands\Concerns\CanAskForLivewireComponentLocation;
use Filament\Support\Commands\Concerns\CanAskForResource;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasCluster;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Concerns\HasResourcesLocation;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\FileGenerators\Concerns\CanCheckFileGenerationFlags;
use Filament\Support\Facades\FilamentCli;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Commands\FileGenerators\ChartWidgetClassGenerator;
use Filament\Widgets\Commands\FileGenerators\CustomWidgetClassGenerator;
use Filament\Widgets\Commands\FileGenerators\StatsOverviewWidgetClassGenerator;
use Filament\Widgets\Commands\FileGenerators\TableWidgetClassGenerator;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Widget;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-widget', aliases: [
    'filament:make-widget',
    'filament:widget',
])]
class MakeWidgetCommand extends Command
{
    use CanAskForLivewireComponentLocation;
    use CanAskForResource;
    use CanAskForViewLocation;
    use CanCheckFileGenerationFlags;
    use CanManipulateFiles;
    use HasCluster;
    use HasPanel;
    use HasResourcesLocation;

    protected $description = 'Create a new Filament widget class';

    protected $name = 'make:filament-widget';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:make-widget',
        'filament:widget',
    ];

    /**
     * @var class-string
     */
    protected string $fqn;

    protected string $fqnEnd;

    protected ?string $view = null;

    protected ?string $viewPath = null;

    protected bool $hasResource;

    /**
     * @var ?class-string
     */
    protected ?string $resourceFqn = null;

    /**
     * @var class-string<Widget> | null
     */
    protected ?string $type = null;

    protected string $widgetsNamespace;

    protected string $widgetsDirectory;

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the widget to generate, optionally prefixed with directories',
            ),
        ];
    }

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'chart',
                shortcut: 'C',
                mode: InputOption::VALUE_NONE,
                description: 'Create a chart widget',
            ),
            new InputOption(
                name: 'cluster',
                shortcut: null,
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The cluster that the resource belongs to',
            ),
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the widget in',
            ),
            new InputOption(
                name: 'resource',
                shortcut: 'R',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The resource to create the widget in',
            ),
            new InputOption(
                name: 'resource-namespace',
                shortcut: null,
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The namespace of the resource class, such as [' . app()->getNamespace() . 'Filament\\Resources]',
            ),
            new InputOption(
                name: 'stats-overview',
                shortcut: 'S',
                mode: InputOption::VALUE_NONE,
                description: 'Create a stats overview widget',
            ),
            new InputOption(
                name: 'table',
                shortcut: 'T',
                mode: InputOption::VALUE_NONE,
                description: 'Create a table widget',
            ),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function handle(): int
    {
        try {
            $this->configureFqnEnd();
            $this->configureType();
            $this->configurePanel(
                question: 'Which panel would you like to create this widget in?',
                initialQuestion: 'Would you like to create this widget in a panel?',
            );
            $this->configureHasResource();
            $this->configureCluster();
            $this->configureResource();
            $this->configureWidgetsLocation();

            $this->configureLocation();

            $this->createCustomWidget();
            $this->createChartWidget();
            $this->createStatsOverviewWidget();
            $this->createTableWidget();
            $this->createView();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament widget [{$this->fqn}] created successfully.");

        if (filled($this->resourceFqn)) {
            $this->components->info("Make sure to register the widget in [{$this->resourceFqn}::getWidgets()], and add it to a page in the resource.");
        } elseif ($this->panel && empty($this->panel->getWidgetNamespaces())) {
            $this->components->info('Make sure to register the widget with [widgets()] or discover it with [discoverWidgets()] in the panel service provider.');
        }

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the widget name?',
            placeholder: 'BlogPostsChart',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureType(): void
    {
        $this->type = match (true) {
            boolval($this->option('chart')) => ChartWidget::class,
            boolval($this->option('stats-overview')) => StatsOverviewWidget::class,
            boolval($this->option('table')) => TableWidget::class,
            default => null,
        } ?? select(
            label: 'Which type of widget would you like to create?',
            options: [
                Widget::class => 'Custom',
                ChartWidget::class => 'Chart',
                StatsOverviewWidget::class => 'Stats overview',
                TableWidget::class => 'Table',
            ],
        );
    }

    protected function configureHasResource(): void
    {
        if (! $this->panel) {
            $this->hasResource = false;

            return;
        }

        $this->hasResource = $this->option('resource') || confirm(
            label: 'Would you like to create this widget in a resource?',
            default: false,
        );
    }

    protected function configureCluster(): void
    {
        if (! $this->hasResource) {
            return;
        }

        $this->configureClusterFqn(
            initialQuestion: 'Is the resource in a cluster?',
            question: 'Which cluster is the resource in?',
        );

        if (blank($this->clusterFqn)) {
            return;
        }

        $this->configureClusterResourcesLocation();
    }

    protected function configureResource(): void
    {
        if (! $this->hasResource) {
            return;
        }

        $this->configureResourcesLocation(question: 'Which namespace would you like to search for resources in?');

        $this->resourceFqn = $this->askForResource(
            question: 'Which resource would you like to create this widget in?',
            initialResource: $this->option('resource'),
        );

        $pluralResourceBasenameBeforeResource = (string) str($this->resourceFqn)
            ->classBasename()
            ->beforeLast('Resource')
            ->plural();

        $resourceNamespacePartBeforeBasename = (string) str($this->resourceFqn)
            ->beforeLast('\\')
            ->classBasename();

        if ($pluralResourceBasenameBeforeResource === $resourceNamespacePartBeforeBasename) {
            $this->widgetsNamespace = (string) str($this->resourceFqn)
                ->beforeLast('\\')
                ->append('\\Widgets');
            $this->widgetsDirectory = (string) str((new ReflectionClass($this->resourceFqn))->getFileName())
                ->beforeLast(DIRECTORY_SEPARATOR)
                ->append('/Widgets');

            return;
        }

        $this->widgetsNamespace = "{$this->resourceFqn}\\Widgets";
        $this->widgetsDirectory = (string) str((new ReflectionClass($this->resourceFqn))->getFileName())
            ->beforeLast('.')
            ->append('/Widgets');
    }

    protected function configureWidgetsLocation(): void
    {
        if (filled($this->resourceFqn)) {
            return;
        }

        if (! $this->panel) {
            [
                $this->widgetsNamespace,
                $this->widgetsDirectory,
            ] = $this->askForLivewireComponentLocation(
                question: 'Where would you like to create the widget?',
            );

            return;
        }

        $directories = $this->panel->getWidgetDirectories();
        $namespaces = $this->panel->getWidgetNamespaces();

        foreach ($directories as $index => $directory) {
            if (str($directory)->startsWith(base_path('vendor'))) {
                unset($directories[$index]);
                unset($namespaces[$index]);
            }
        }

        if (count($namespaces) < 2) {
            $this->widgetsNamespace = (Arr::first($namespaces) ?? app()->getNamespace() . 'Filament\\Widgets');
            $this->widgetsDirectory = (Arr::first($directories) ?? app_path('Filament/Widgets/'));

            return;
        }

        $keyedNamespaces = array_combine(
            $namespaces,
            $namespaces,
        );

        $this->widgetsNamespace = search(
            label: 'Which namespace would you like to create this widget in?',
            options: function (?string $search) use ($keyedNamespaces): array {
                if (blank($search)) {
                    return $keyedNamespaces;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($keyedNamespaces, fn (string $namespace): bool => str($namespace)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
        );
        $this->widgetsDirectory = $directories[array_search($this->widgetsNamespace, $namespaces)];
    }

    protected function configureLocation(): void
    {
        $this->fqn = $this->widgetsNamespace . '\\' . $this->fqnEnd;

        if ($this->type === Widget::class) {
            $componentLocations = FilamentCli::getLivewireComponentLocations();

            $matchingComponentLocationNamespaces = collect($componentLocations)
                ->keys()
                ->filter(fn (string $namespace): bool => str($this->fqn)->startsWith($namespace));

            [
                $this->view,
                $this->viewPath,
            ] = $this->askForViewLocation(
                view: str($this->fqn)
                    ->whenContains(
                        'Filament\\',
                        fn (Stringable $fqn) => $fqn->after('Filament\\')->prepend('Filament\\'),
                        fn (Stringable $fqn) => $fqn
                            ->afterLast('\\Livewire\\')
                            ->prepend('Livewire\\'),
                    )
                    ->replace('\\', '/')
                    ->explode('/')
                    ->map(Str::kebab(...))
                    ->implode('.'),
                question: 'Where would you like to create the Blade view for the widget?',
                defaultNamespace: (count($matchingComponentLocationNamespaces) === 1)
                    ? $componentLocations[Arr::first($matchingComponentLocationNamespaces)]['viewNamespace'] ?? null
                    : null,
            );
        }
    }

    protected function createCustomWidget(): void
    {
        if ($this->type !== Widget::class) {
            return;
        }

        $path = (string) str("{$this->widgetsDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(CustomWidgetClassGenerator::class, [
            'fqn' => $this->fqn,
            'view' => $this->view,
        ]));
    }

    protected function createChartWidget(): void
    {
        if ($this->type !== ChartWidget::class) {
            return;
        }

        $type = select(
            label: 'Which type of chart would you like to create?',
            options: [
                'bar' => 'Bar chart',
                'bubble' => 'Bubble chart',
                'doughnut' => 'Doughnut chart',
                'line' => 'Line chart',
                'pie' => 'Pie chart',
                'polarArea' => 'Polar area chart',
                'radar' => 'Radar chart',
                'scatter' => 'Scatter chart',
            ],
            default: 'line',
        );

        $path = (string) str("{$this->widgetsDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ChartWidgetClassGenerator::class, [
            'fqn' => $this->fqn,
            'type' => $type,
        ]));
    }

    protected function createStatsOverviewWidget(): void
    {
        if ($this->type !== StatsOverviewWidget::class) {
            return;
        }

        $path = (string) str("{$this->widgetsDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(StatsOverviewWidgetClassGenerator::class, [
            'fqn' => $this->fqn,
        ]));
    }

    protected function createTableWidget(): void
    {
        if ($this->type !== TableWidget::class) {
            return;
        }

        $modelFqns = discover_app_classes(parentClass: Model::class);

        $modelFqn = suggest(
            label: 'What is the model?',
            options: function (string $search) use ($modelFqns): array {
                $search = str($search)->trim()->replace(['\\', '/'], '');

                if (blank($search)) {
                    return $modelFqns;
                }

                return array_filter(
                    $modelFqns,
                    fn (string $class): bool => str($class)->replace(['\\', '/'], '')->contains($search, ignoreCase: true),
                );
            },
            placeholder: app()->getNamespace() . 'Models\\BlogPost',
        );

        $isGenerated = confirm(
            label: 'Should the table columns be generated from the current database columns?',
            default: false,
        );

        $path = (string) str("{$this->widgetsDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(TableWidgetClassGenerator::class, [
            'fqn' => $this->fqn,
            'modelFqn' => $modelFqn ?: Model::class,
            'isGenerated' => $isGenerated,
        ]));
    }

    protected function createView(): void
    {
        if (blank($this->view)) {
            return;
        }

        if (! $this->option('force') && $this->checkForCollision($this->viewPath)) {
            throw new FailureCommandOutput;
        }

        $this->copyStubToApp('WidgetView', $this->viewPath);
    }
}
