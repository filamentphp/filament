<?php

namespace Filament\Forms\Commands;

use Filament\Forms\Commands\FileGenerators\FieldClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForComponentLocation;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-form-field', aliases: [
    'filament:field',
    'filament:form-field',
    'forms:field',
    'forms:make-field',
    'make:filament-field',
    'make:form-field',
])]
class MakeFieldCommand extends Command
{
    use CanAskForComponentLocation;
    use CanAskForViewLocation;
    use CanManipulateFiles;

    protected $description = 'Create a new form field class and view';

    protected $name = 'make:filament-form-field';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:field',
        'filament:form-field',
        'forms:field',
        'forms:make-field',
        'make:filament-field',
        'make:form-field',
    ];

    protected string $fqnEnd;

    protected string $fqn;

    protected string $path;

    protected string $view;

    protected string $viewPath;

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the field to generate, optionally prefixed with directories',
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

            $this->createField();
            $this->createView();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament form field [{$this->fqn}] created successfully.");

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the field name?',
            placeholder: 'RangeSlider',
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
            $viewNamespace,
        ] = $this->askForComponentLocation(
            path: 'Forms/Components',
            question: 'Where would you like to create the field?',
        );

        $this->fqn = "{$namespace}\\{$this->fqnEnd}";
        $this->path = (string) str("{$path}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        [
            $this->view,
            $this->viewPath,
        ] = $this->askForViewLocation(
            str($this->fqn)
                ->afterLast('\\Forms\\Components\\')
                ->prepend('Filament\\Forms\\Components\\')
                ->replace('\\', '/')
                ->explode('/')
                ->map(Str::kebab(...))
                ->implode('.'),
            defaultNamespace: $viewNamespace,
        );
    }

    protected function createField(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(FieldClassGenerator::class, [
            'fqn' => $this->fqn,
            'view' => $this->view,
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

        $this->copyStubToApp('FieldView', $this->viewPath);
    }
}
