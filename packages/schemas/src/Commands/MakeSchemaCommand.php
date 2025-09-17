<?php

namespace Filament\Schemas\Commands;

use Filament\Schemas\Commands\FileGenerators\SchemaClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForComponentLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-schema', aliases: [
    'filament:infolist',
    'filament:schema',
    'make:filament-infolist',
])]
class MakeSchemaCommand extends Command
{
    use CanAskForComponentLocation;
    use CanManipulateFiles;

    protected $description = 'Create a new Filament schema class';

    protected $name = 'make:filament-schema';

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
        'filament:infolist',
        'filament:schema',
        'make:filament-infolist',
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
                description: 'The name of the schema class to generate, optionally prefixed with directories',
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

            $this->configureLocation();

            $this->createSchema();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Schema [{$this->fqn}] created successfully.");

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the schema name?',
            placeholder: 'BlogPostSchema',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureLocation(): void
    {
        [
            $namespace,
            $path,
        ] = $this->askForComponentLocation(
            path: 'Schemas',
            question: 'Where would you like to create the schema?',
        );

        $this->fqn = "{$namespace}\\{$this->fqnEnd}";
        $this->path = (string) str("{$path}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');
    }

    protected function createSchema(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(SchemaClassGenerator::class, [
            'fqn' => $this->fqn,
        ]));
    }
}
