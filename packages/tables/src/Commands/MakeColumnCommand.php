<?php

namespace Filament\Tables\Commands;

use Filament\Support\Commands\Concerns\CanAskForComponentLocation;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Tables\Commands\FileGenerators\ColumnClassGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-table-column', aliases: [
    'filament:column',
    'filament:table-column',
    'make:table-column',
])]
class MakeColumnCommand extends Command
{
    use CanAskForComponentLocation;
    use CanAskForViewLocation;
    use CanManipulateFiles;

    protected $description = 'Create a new table column class and cell view';

    protected $name = 'make:filament-table-column';

    protected string $fqnEnd;

    protected string $fqn;

    protected string $path;

    protected bool $hasEmbeddedView;

    protected ?string $view = null;

    protected ?string $viewPath = null;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:column',
        'filament:table-column',
        'make:table-column',
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
                description: 'The name of the column to generate, optionally prefixed with directories',
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
                name: 'embedded-view',
                shortcut: 'E',
                mode: InputOption::VALUE_NONE,
                description: 'Define embedded HTML inside the class instead of using a separate Blade view file',
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
            $this->configureHasEmbeddedView();

            $this->configureLocation();

            $this->createColumn();
            $this->createView();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament table column [{$this->fqn}] created successfully.");

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the column name?',
            placeholder: 'StatusSwitcherColumn',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureHasEmbeddedView(): void
    {
        $this->hasEmbeddedView = $this->option('embedded-view') || confirm(
            label: 'Do you want to embed the HTML of the view in the column class?',
            default: false,
            hint: 'Defining the HTML of the column in the class instead of in a Blade view file improves the performance of the column, but doesn\'t allow you to use Blade syntax.',
        );
    }

    protected function configureLocation(): void
    {
        [
            $namespace,
            $path,
            $viewNamespace,
        ] = $this->askForComponentLocation(
            path: 'Tables/Columns',
            question: 'Where would you like to create the column?',
        );

        $this->fqn = "{$namespace}\\{$this->fqnEnd}";
        $this->path = (string) str("{$path}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if ($this->hasEmbeddedView) {
            return;
        }

        [
            $this->view,
            $this->viewPath,
        ] = $this->askForViewLocation(
            str($this->fqn)
                ->afterLast('\\Tables\\Columns\\')
                ->prepend('Filament\\Tables\\Columns\\')
                ->replace('\\', '/')
                ->explode('/')
                ->map(Str::kebab(...))
                ->implode('.'),
            defaultNamespace: $viewNamespace,
        );
    }

    protected function createColumn(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(ColumnClassGenerator::class, [
            'fqn' => $this->fqn,
            'hasEmbeddedView' => $this->hasEmbeddedView,
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

        $this->copyStubToApp('ColumnView', $this->viewPath);
    }
}
