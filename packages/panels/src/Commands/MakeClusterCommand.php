<?php

namespace Filament\Commands;

use Filament\Commands\FileGenerators\ClusterClassGenerator;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\FileGenerators\Concerns\CanCheckFileGenerationFlags;
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-cluster', aliases: [
    'filament:cluster',
    'filament:make-cluster',
])]
class MakeClusterCommand extends Command
{
    use CanCheckFileGenerationFlags;
    use CanManipulateFiles;
    use HasPanel;

    protected $description = 'Create a new Filament cluster class';

    protected $name = 'make:filament-cluster';

    protected bool $hasClusterClassesOutsideDirectories;

    /**
     * @var class-string
     */
    protected string $fqn;

    protected string $fqnEnd;

    protected string $clustersNamespace;

    protected string $clustersDirectory;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:cluster',
        'filament:make-cluster',
    ];

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the cluster to generate, optionally prefixed with directories',
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
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the cluster in',
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
            $this->configureHasClusterClassesOutsideDirectories();
            $this->configureFqnEnd();
            $this->configurePanel(question: 'Which panel would you like to create this cluster in?');
            $this->configureClustersLocation();

            $this->configureFqn();

            $this->createClass();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament cluster [{$this->fqn}] created successfully.");

        if (empty($this->panel->getClusterNamespaces())) {
            $this->components->info('Make sure to register the cluster with [clusters()] or discover it with [discoverClusters()] in the panel service provider.');
        }

        return static::SUCCESS;
    }

    protected function configureHasClusterClassesOutsideDirectories(): void
    {
        $this->hasClusterClassesOutsideDirectories = $this->hasFileGenerationFlag(FileGenerationFlag::PANEL_CLUSTER_CLASSES_OUTSIDE_DIRECTORIES);
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the cluster name?',
            placeholder: 'Settings',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');

        if ($this->hasClusterClassesOutsideDirectories) {
            return;
        }

        if (
            str($this->fqnEnd)->endsWith('Cluster') &&
            (! str($this->fqnEnd)->endsWith('\\Cluster'))
        ) {
            $this->fqnEnd = (string) str($this->fqnEnd)
                ->beforeLast('Cluster');
        }

        $this->fqnEnd .= '\\' . str($this->fqnEnd)
            ->classBasename()
            ->append('Cluster');
    }

    protected function configureClustersLocation(): void
    {
        $directories = $this->panel->getClusterDirectories();
        $namespaces = $this->panel->getClusterNamespaces();

        foreach ($directories as $index => $directory) {
            if (str($directory)->startsWith(base_path('vendor'))) {
                unset($directories[$index]);
                unset($namespaces[$index]);
            }
        }

        if (count($namespaces) < 2) {
            $this->clustersNamespace = (Arr::first($namespaces) ?? app()->getNamespace() . 'Filament\\Clusters');
            $this->clustersDirectory = (Arr::first($directories) ?? app_path('Filament/Clusters/'));

            return;
        }

        $keyedNamespaces = array_combine(
            $namespaces,
            $namespaces,
        );

        $this->clustersNamespace = search(
            label: 'Which namespace would you like to create this cluster in?',
            options: function (?string $search) use ($keyedNamespaces): array {
                if (blank($search)) {
                    return $keyedNamespaces;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($keyedNamespaces, fn (string $namespace): bool => str($namespace)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
        );
        $this->clustersDirectory = $directories[array_search($this->clustersNamespace, $namespaces)];
    }

    protected function configureFqn(): void
    {
        $this->fqn = $this->clustersNamespace . '\\' . $this->fqnEnd;
    }

    protected function createClass(): void
    {
        $path = (string) str("{$this->clustersDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ClusterClassGenerator::class, [
            'fqn' => $this->fqn,
        ]));
    }
}
