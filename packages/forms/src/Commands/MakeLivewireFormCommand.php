<?php

namespace Filament\Forms\Commands;

use Filament\Forms\Commands\FileGenerators\LivewireFormComponentClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForLivewireComponentLocation;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-livewire-form', aliases: [
    'filament:livewire-form',
    'make:livewire-form',
])]
class MakeLivewireFormCommand extends Command
{
    use CanAskForLivewireComponentLocation;
    use CanAskForViewLocation;
    use CanManipulateFiles;

    protected $description = 'Create a new Livewire component containing a Filament form';

    protected $name = 'make:filament-livewire-form';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:livewire-form',
        'make:livewire-form',
    ];

    /**
     * @var class-string
     */
    protected string $fqn;

    protected string $fqnEnd;

    protected string $path;

    /**
     * @var ?class-string<Model>
     */
    protected ?string $modelFqn = null;

    protected ?string $modelFqnEnd = null;

    protected ?bool $isEdit = null;

    protected ?bool $isGenerated = null;

    protected string $submitAction;

    protected ?string $view = null;

    protected ?string $viewPath = null;

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the Livewire component to generate, optionally prefixed with directories',
            ),
            new InputArgument(
                name: 'model',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the model to generate the form for, optionally prefixed with directories',
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
                name: 'edit',
                shortcut: 'E',
                mode: InputOption::VALUE_NONE,
                description: 'Generate the form to edit the model instead of creating it',
            ),
            new InputOption(
                name: 'generate',
                shortcut: 'G',
                mode: InputOption::VALUE_NONE,
                description: 'Generate the form fields based on the attributes of a model',
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
            $this->configureFqnEnd();
            $this->configureModel();
            $this->configureIsEdit();
            $this->configureIsGenerated();

            $this->configureLocation();
            $this->configureSubmitAction();

            $this->createLivewireComponent();
            $this->createView();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Livewire component [{$this->fqn}] created successfully.");

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the Livewire component name?',
            placeholder: 'CreateBlogPost',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureModel(): void
    {
        if ($this->argument('model')) {
            $this->modelFqnEnd = (string) str($this->argument('model'))
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->studly()
                ->replace('/', '\\');

            $modelNamespace = $this->option('model-namespace') ?? app()->getNamespace() . 'Models';

            $this->modelFqn = "{$modelNamespace}\\{$this->modelFqnEnd}";

            return;
        }

        if (! confirm(
            label: 'Would you like to create a form for a model?',
            default: false,
        )) {
            return;
        }

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

    protected function configureIsEdit(): void
    {
        if (blank($this->modelFqn)) {
            return;
        }

        $this->isEdit = $this->option('edit') || (select(
            label: 'What would you like the form to do?',
            options: [
                'create' => 'Create a new model record',
                'edit' => 'Edit an existing model record',
            ],
            default: 'create',
        ) === 'edit');
    }

    protected function configureIsGenerated(): void
    {
        if (blank($this->modelFqn)) {
            return;
        }

        $this->isGenerated = $this->option('generate') || confirm(
            label: 'Should the form fields be generated from the current database columns?',
            default: false,
        );
    }

    protected function configureLocation(): void
    {
        [
            $namespace,
            $path,
            $viewNamespace,
        ] = $this->askForLivewireComponentLocation(
            question: 'Where would you like to create the form?',
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
                ->afterLast('\\Livewire\\')
                ->prepend('Livewire\\')
                ->replace('\\', '/')
                ->explode('/')
                ->map(Str::kebab(...))
                ->implode('.'),
            defaultNamespace: $viewNamespace,
        );
    }

    protected function configureSubmitAction(): void
    {
        $this->submitAction = filled($this->modelFqn)
            ? ($this->isEdit ? 'save' : 'create')
            : 'submit';
    }

    protected function createLivewireComponent(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(LivewireFormComponentClassGenerator::class, [
            'fqn' => $this->fqn,
            'submitAction' => $this->submitAction,
            'modelFqn' => $this->modelFqn,
            'isGenerated' => $this->isGenerated,
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

        $this->copyStubToApp('LivewireFormView', $this->viewPath, [
            'submitAction' => $this->submitAction,
        ]);
    }
}
