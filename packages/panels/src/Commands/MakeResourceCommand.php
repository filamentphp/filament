<?php

namespace Filament\Commands;

use Filament\Commands\FileGenerators\Resources\Pages\ResourceCreateRecordPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceEditRecordPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceListRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceViewRecordPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceFormSchemaClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceInfolistSchemaClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;
use Filament\Resources\Pages\Page;
use Filament\Support\Commands\Concerns\CanAskForResource;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasCluster;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Concerns\HasResourcesLocation;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\FileGenerators\Concerns\CanCheckFileGenerationFlags;
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-resource', aliases: [
    'filament:make-resource',
    'filament:resource',
])]
class MakeResourceCommand extends Command
{
    use CanAskForResource;
    use CanCheckFileGenerationFlags;
    use CanManipulateFiles;
    use HasCluster;
    use HasPanel;
    use HasResourcesLocation;

    protected $description = 'Create a new Filament resource class and default page classes';

    protected $name = 'make:filament-resource';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:make-resource',
        'filament:resource',
    ];

    /**
     * @var class-string<Model>
     */
    protected string $modelFqn;

    protected string $modelFqnEnd;

    /**
     * @var ?class-string
     */
    protected ?string $parentResourceFqn = null;

    /**
     * @var class-string
     */
    protected string $fqn;

    protected string $fqnEnd;

    /**
     * @var array<string, array{
     *      class: class-string<Page>,
     *      path: string,
     * }>
     */
    protected array $pageRoutes;

    protected string $namespace;

    protected string $directory;

    protected ?string $formSchemaFqn = null;

    protected ?string $infolistSchemaFqn = null;

    protected ?string $tableFqn = null;

    protected ?string $recordTitleAttribute = null;

    protected bool $hasViewOperation;

    protected bool $isGenerated;

    protected bool $isSimple;

    protected bool $isSoftDeletable;

    protected bool $hasResourceClassesOutsideDirectories;

    public static bool $shouldCheckModelsForSoftDeletes = true;

    protected bool $isNested;

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'model',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the model to generate the resource for, optionally prefixed with directories',
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
                name: 'cluster',
                shortcut: 'C',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The cluster to create the resource in',
            ),
            new InputOption(
                name: 'embed-schemas',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Embed the form and infolist schemas in the resource class instead of creating separate files',
            ),
            new InputOption(
                name: 'embed-table',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Embed the table in the resource class instead of creating a separate file',
            ),
            new InputOption(
                name: 'factory',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Create a factory for the model',
            ),
            new InputOption(
                name: 'generate',
                shortcut: 'G',
                mode: InputOption::VALUE_NONE,
                description: 'Generate the form schema and table columns from the current database columns',
            ),
            new InputOption(
                name: 'migration',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Create a migration for the model',
            ),
            new InputOption(
                name: 'model',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Create the model class if it does not exist',
            ),
            new InputOption(
                name: 'model-namespace',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The namespace of the model class, [' . app()->getNamespace() . 'Models] by default',
            ),
            new InputOption(
                name: 'nested',
                shortcut: 'N',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Nest the resource inside another through a relationship',
                default: false,
            ),
            new InputOption(
                name: 'not-embedded',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Even if the resource is simple, create separate files for the form and infolist schemas and table',
            ),
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the resource in',
            ),
            new InputOption(
                name: 'record-title-attribute',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The title attribute, used to label each record in the UI',
            ),
            new InputOption(
                name: 'resource-namespace',
                shortcut: null,
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The namespace of the resource class, such as [' . app()->getNamespace() . 'Filament\\Resources]',
            ),
            new InputOption(
                name: 'simple',
                shortcut: 'S',
                mode: InputOption::VALUE_NONE,
                description: 'Generate a simple resource class with a single page, modals and embedded schemas and embedded table',
            ),
            new InputOption(
                name: 'soft-deletes',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Indicate if the model uses soft-deletes',
            ),
            new InputOption(
                name: 'view',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Generate a view page / modal for the resource',
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
            $this->configureRecordTitleAttribute();
            $this->configurePanel(question: 'Which panel would you like to create this resource in?');
            $this->configureIsSimple();
            $this->configureIsNested();
            $this->configureCluster();
            $this->configureResourcesLocation(question: 'Which namespace would you like to create this resource in?');
            $this->configureParentResource();
            $this->configureHasViewOperation();
            $this->configureIsGenerated();
            $this->configureIsSoftDeletable();
            $this->configureHasResourceClassesOutsideDirectories();

            $this->configureLocation();
            $this->configurePageRoutes();

            $this->createFormSchema();
            $this->createInfolistSchema();
            $this->createTable();

            $this->createResourceClass();

            $this->createManagePage();
            $this->createListPage();
            $this->createCreatePage();
            $this->createEditPage();
            $this->createViewPage();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament resource [{$this->fqn}] created successfully.");

        if (empty($this->panel->getResourceNamespaces())) {
            $this->components->info('Make sure to register the resource with [resources()] or discover it with [discoverResources()] in the panel service provider.');
        }

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
                    fn (Stringable $model): bool => str($model)->endsWith('Resource'),
                    fn (Stringable $model): Stringable => str($model)->beforeLast('Resource'),
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

        if ($this->option('model')) {
            $this->callSilently('make:model', [
                'name' => $this->modelFqn,
            ]);
        }

        if ($this->option('migration')) {
            $table = (string) str($this->modelFqn)
                ->classBasename()
                ->pluralStudly()
                ->snake();

            $this->call('make:migration', [
                'name' => "create_{$table}_table",
                '--create' => $table,
            ]);
        }

        if ($this->option('factory')) {
            $this->callSilently('make:factory', [
                'name' => $this->modelFqnEnd,
            ]);
        }
    }

    protected function configureRecordTitleAttribute(): void
    {
        $this->recordTitleAttribute = $this->option('record-title-attribute');

        if (filled($this->recordTitleAttribute)) {
            return;
        }

        info('The "title attribute" is used to label each record in the UI.');

        info('You can leave this blank if records do not have a title.');

        $this->recordTitleAttribute = text(
            label: 'What is the title attribute for this model?',
            placeholder: 'name',
        );
    }

    protected function configureIsSimple(): void
    {
        $this->isSimple = $this->option('simple');
    }

    protected function configureIsNested(): void
    {
        $this->isNested = $this->option('nested') !== false;

        if ($this->isNested && $this->isSimple) {
            $this->components->error('Nested resources cannot be simple, you can use the relation manager or relation page on the parent resource to open modals for each operation.');

            throw new FailureCommandOutput;
        }
    }

    protected function configureCluster(): void
    {
        if ($this->isNested) {
            $this->configureClusterFqn(
                initialQuestion: 'Is the parent resource in a cluster?',
                question: 'Which cluster is the parent resource in?',
            );
        } else {
            $this->configureClusterFqn(
                initialQuestion: 'Would you like to create this resource in a cluster?',
                question: 'Which cluster would you like to create this resource in?',
            );
        }

        if (blank($this->clusterFqn)) {
            return;
        }

        $this->configureClusterResourcesLocation();
    }

    protected function configureParentResource(): void
    {
        if (! $this->isNested) {
            return;
        }

        $this->parentResourceFqn = $this->askForResource(
            question: 'Which resource would you like to nest this resource inside?',
            initialResource: $this->option('nested'),
        );

        $pluralParentResourceBasenameBeforeResource = (string) str($this->parentResourceFqn)
            ->classBasename()
            ->beforeLast('Resource')
            ->plural();

        $parentResourceNamespacePartBeforeBasename = (string) str($this->parentResourceFqn)
            ->beforeLast('\\')
            ->classBasename();

        if ($pluralParentResourceBasenameBeforeResource === $parentResourceNamespacePartBeforeBasename) {
            $this->resourcesNamespace = (string) str($this->parentResourceFqn)
                ->beforeLast('\\')
                ->append('\\Resources');
            $this->resourcesDirectory = (string) str((new ReflectionClass($this->parentResourceFqn))->getFileName())
                ->beforeLast(DIRECTORY_SEPARATOR)
                ->append('/Resources');

            return;
        }

        $this->resourcesNamespace = "{$this->parentResourceFqn}\\Resources";
        $this->resourcesDirectory = (string) str((new ReflectionClass($this->parentResourceFqn))->getFileName())
            ->beforeLast('.')
            ->append('/Resources');
    }

    protected function configureHasViewOperation(): void
    {
        $this->hasViewOperation = $this->option('view') || confirm(
            label: $this->isSimple
                ? 'Would you like to generate a read-only view modal for the resource?'
                : 'Would you like to generate a read-only view page for the resource?',
            default: false,
        );
    }

    protected function configureIsGenerated(): void
    {
        $this->isGenerated = $this->option('generate') || confirm(
            label: 'Should the configuration be generated from the current database columns?',
            default: false,
        );
    }

    protected function configureIsSoftDeletable(): void
    {
        $this->isSoftDeletable = $this->option('soft-deletes') || ((static::$shouldCheckModelsForSoftDeletes && class_exists($this->modelFqn))
            ? in_array(SoftDeletes::class, class_uses_recursive($this->modelFqn))
            : confirm(
                label: 'Does the model use soft-deletes?',
                default: false,
            ));
    }

    protected function configureHasResourceClassesOutsideDirectories(): void
    {
        $this->hasResourceClassesOutsideDirectories = $this->hasFileGenerationFlag(FileGenerationFlag::PANEL_RESOURCE_CLASSES_OUTSIDE_DIRECTORIES);
    }

    protected function configureLocation(): void
    {
        if ($this->hasResourceClassesOutsideDirectories) {
            $this->fqnEnd = "{$this->modelFqnEnd}Resource";
        } else {
            $this->fqnEnd = Str::pluralStudly($this->modelFqnEnd) . '\\' . class_basename($this->modelFqn) . 'Resource';
        }

        $this->fqn = $this->resourcesNamespace . '\\' . $this->fqnEnd;

        if ($this->hasResourceClassesOutsideDirectories) {
            $this->namespace = $this->fqn;
            $this->directory = (string) str("{$this->resourcesDirectory}/{$this->fqnEnd}")
                ->replace('\\', '/')
                ->replace('//', '/');
        } else {
            $this->namespace = (string) str($this->fqn)
                ->beforeLast('\\');
            $this->directory = (string) str($this->resourcesDirectory . '/' . Str::pluralStudly($this->modelFqnEnd))
                ->replace('\\', '/')
                ->replace('//', '/');
        }
    }

    protected function configurePageRoutes(): void
    {
        $modelBasename = class_basename($this->modelFqn);
        $pluralModelBasename = Str::pluralStudly($modelBasename);

        if ($this->isSimple) {
            $this->pageRoutes = [
                'index' => [
                    'class' => "{$this->namespace}\\Pages\\Manage{$pluralModelBasename}",
                    'path' => '/',
                ],
            ];

            return;
        }

        $this->pageRoutes = [
            ...(blank($this->parentResourceFqn) ? [
                'index' => [
                    'class' => "{$this->namespace}\\Pages\\List{$pluralModelBasename}",
                    'path' => '/',
                ],
            ] : []),
            'create' => [
                'class' => "{$this->namespace}\\Pages\\Create{$modelBasename}",
                'path' => '/create',
            ],
            ...($this->hasViewOperation ? [
                'view' => [
                    'class' => "{$this->namespace}\\Pages\\View{$modelBasename}",
                    'path' => '/{record}',
                ],
            ] : []),
            'edit' => [
                'class' => "{$this->namespace}\\Pages\\Edit{$modelBasename}",
                'path' => '/{record}/edit',
            ],
        ];
    }

    protected function createFormSchema(): void
    {
        if ($this->hasEmbeddedSchemas()) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);

        $path = "{$this->directory}/Schemas/{$modelBasename}Form.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->formSchemaFqn = "{$this->namespace}\\Schemas\\{$modelBasename}Form";

        $this->writeFile($path, app(ResourceFormSchemaClassGenerator::class, [
            'fqn' => $this->formSchemaFqn,
            'modelFqn' => $this->modelFqn,
            'parentResourceFqn' => $this->parentResourceFqn,
            'isGenerated' => $this->isGenerated,
        ]));
    }

    protected function createInfolistSchema(): void
    {
        if (! $this->hasViewOperation) {
            return;
        }

        if ($this->hasEmbeddedSchemas()) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);

        $path = "{$this->directory}/Schemas/{$modelBasename}Infolist.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->infolistSchemaFqn = "{$this->namespace}\\Schemas\\{$modelBasename}Infolist";

        $this->writeFile($path, app(ResourceInfolistSchemaClassGenerator::class, [
            'fqn' => $this->infolistSchemaFqn,
            'modelFqn' => $this->modelFqn,
            'parentResourceFqn' => $this->parentResourceFqn,
            'isGenerated' => $this->isGenerated,
        ]));
    }

    protected function createTable(): void
    {
        if ($this->hasEmbeddedTable()) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);
        $pluralModelBasename = Str::pluralStudly($modelBasename);

        $path = "{$this->directory}/Tables/{$pluralModelBasename}Table.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->tableFqn = "{$this->namespace}\\Tables\\{$pluralModelBasename}Table";

        $this->writeFile($path, app(ResourceTableClassGenerator::class, [
            'fqn' => $this->tableFqn,
            'modelFqn' => $this->modelFqn,
            'parentResourceFqn' => $this->parentResourceFqn,
            'hasViewOperation' => $this->hasViewOperation,
            'isGenerated' => $this->isGenerated,
            'isSoftDeletable' => $this->isSoftDeletable,
            'isSimple' => $this->isSimple,
        ]));
    }

    protected function createResourceClass(): void
    {
        $path = (string) str("{$this->resourcesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceClassGenerator::class, [
            'fqn' => $this->fqn,
            'modelFqn' => $this->modelFqn,
            'clusterFqn' => $this->clusterFqn,
            'parentResourceFqn' => $this->parentResourceFqn,
            'pageRoutes' => $this->pageRoutes,
            'formSchemaFqn' => $this->formSchemaFqn,
            'infolistSchemaFqn' => $this->infolistSchemaFqn,
            'tableFqn' => $this->tableFqn,
            'recordTitleAttribute' => $this->recordTitleAttribute,
            'hasViewOperation' => $this->hasViewOperation,
            'isGenerated' => $this->isGenerated,
            'isSoftDeletable' => $this->isSoftDeletable,
            'isSimple' => $this->isSimple,
        ]));
    }

    protected function createManagePage(): void
    {
        if (! $this->isSimple) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);
        $pluralModelBasename = Str::pluralStudly($modelBasename);

        $path = "{$this->directory}/Pages/Manage{$pluralModelBasename}.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceManageRecordsPageClassGenerator::class, [
            'fqn' => "{$this->namespace}\\Pages\\Manage{$pluralModelBasename}",
            'resourceFqn' => $this->fqn,
        ]));
    }

    protected function createListPage(): void
    {
        if ($this->isSimple) {
            return;
        }

        if (filled($this->parentResourceFqn)) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);
        $pluralModelBasename = Str::pluralStudly($modelBasename);

        $path = "{$this->directory}/Pages/List{$pluralModelBasename}.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceListRecordsPageClassGenerator::class, [
            'fqn' => "{$this->namespace}\\Pages\\List{$pluralModelBasename}",
            'resourceFqn' => $this->fqn,
        ]));
    }

    protected function createCreatePage(): void
    {
        if ($this->isSimple) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);

        $path = "{$this->directory}/Pages/Create{$modelBasename}.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceCreateRecordPageClassGenerator::class, [
            'fqn' => "{$this->namespace}\\Pages\\Create{$modelBasename}",
            'resourceFqn' => $this->fqn,
        ]));
    }

    protected function createEditPage(): void
    {
        if ($this->isSimple) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);

        $path = "{$this->directory}/Pages/Edit{$modelBasename}.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceEditRecordPageClassGenerator::class, [
            'fqn' => "{$this->namespace}\\Pages\\Edit{$modelBasename}",
            'resourceFqn' => $this->fqn,
            'hasViewOperation' => $this->hasViewOperation,
            'isSoftDeletable' => $this->isSoftDeletable,
        ]));
    }

    protected function createViewPage(): void
    {
        if (! $this->hasViewOperation) {
            return;
        }

        if ($this->isSimple) {
            return;
        }

        $modelBasename = class_basename($this->modelFqn);

        $path = "{$this->directory}/Pages/View{$modelBasename}.php";

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceViewRecordPageClassGenerator::class, [
            'fqn' => "{$this->namespace}\\Pages\\View{$modelBasename}",
            'resourceFqn' => $this->fqn,
        ]));
    }

    protected function hasEmbeddedSchemas(): bool
    {
        if ($this->isSimple && (! $this->option('not-embedded'))) {
            return true;
        }

        if ($this->option('embed-schemas')) {
            return true;
        }

        return $this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS);
    }

    protected function hasEmbeddedTable(): bool
    {
        if ($this->isSimple && (! $this->option('not-embedded'))) {
            return true;
        }

        if ($this->option('embed-table')) {
            return true;
        }

        return $this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES);
    }
}
