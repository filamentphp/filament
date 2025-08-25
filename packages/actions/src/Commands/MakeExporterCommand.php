<?php

namespace Filament\Actions\Commands;

use Filament\Actions\Commands\FileGenerators\ExporterClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForComponentLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\suggest;

#[AsCommand(name: 'make:filament-exporter', aliases: [
    'filament:exporter',
])]
class MakeExporterCommand extends Command
{
    use CanAskForComponentLocation;
    use CanManipulateFiles;

    protected $description = 'Create a new Filament exporter class';

    protected $name = 'make:filament-exporter';

    /**
     * @var class-string<Model>
     */
    protected string $modelFqn;

    protected string $modelFqnEnd;

    protected bool $isGenerated;

    /**
     * @var class-string
     */
    protected string $fqn;

    protected string $fqnEnd;

    protected string $path;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:exporter',
    ];

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'model',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the model to generate the exporter for, optionally prefixed with directories',
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
                name: 'generate',
                shortcut: 'G',
                mode: InputOption::VALUE_NONE,
                description: 'Generate the exporter columns based on the attributes of a model',
            ),
            new InputOption(
                name: 'model-namespace',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The namespace of the model class, [' . app()->getNamespace() . 'Models] by default',
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
            $this->configureModel();
            $this->configureIsGenerated();

            $this->configureLocation();

            $this->createExporter();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Exporter [{$this->fqn}] created successfully.");

        return static::SUCCESS;
    }

    protected function configureModel(): void
    {
        if ($this->argument('model')) {
            $this->modelFqnEnd = (string) str($this->argument('model'))
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->when(
                    fn (Stringable $model): bool => str($model)->endsWith('Exporter'),
                    fn (Stringable $model): Stringable => str($model)->beforeLast('Exporter'),
                )
                ->studly()
                ->replace('/', '\\');

            if (blank($this->modelFqnEnd)) {
                $this->modelFqnEnd = 'Resource';
            }

            $modelNamespace = $this->option('model-namespace') ?? app()->getNamespace() . 'Models';

            $this->modelFqn = "{$modelNamespace}\\{$this->modelFqnEnd}";
        } else {
            $modelFqns = discover_app_classes(parentClass: Model::class);

            $this->modelFqn = suggest(
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
                required: true,
            );

            $this->modelFqnEnd = class_basename($this->modelFqn);
        }
    }

    protected function configureIsGenerated(): void
    {
        $this->isGenerated = $this->option('generate') || confirm(
            label: 'Should the exporter columns be generated from the current database columns?',
            default: false,
        );
    }

    protected function configureLocation(): void
    {
        [
            $namespace,
            $path,
        ] = $this->askForComponentLocation(
            path: 'Exports',
            question: 'Where would you like to create the exporter?',
        );

        $this->fqnEnd = "{$this->modelFqnEnd}Exporter";
        $this->fqn = "{$namespace}\\{$this->fqnEnd}";
        $this->path = (string) str("{$path}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');
    }

    protected function createExporter(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(ExporterClassGenerator::class, [
            'fqn' => $this->fqn,
            'modelFqn' => $this->modelFqn,
            'isGenerated' => $this->isGenerated,
        ]));
    }
}
