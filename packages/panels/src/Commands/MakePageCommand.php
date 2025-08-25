<?php

namespace Filament\Commands;

use Filament\Commands\FileGenerators\CustomPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceCreateRecordPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceCustomPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceEditRecordPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRelatedRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceViewRecordPageClassGenerator;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\Page as ResourcePage;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Commands\Concerns\CanAskForRelatedModel;
use Filament\Support\Commands\Concerns\CanAskForRelatedResource;
use Filament\Support\Commands\Concerns\CanAskForResource;
use Filament\Support\Commands\Concerns\CanAskForSchema;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasCluster;
use Filament\Support\Commands\Concerns\HasClusterPagesLocation;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Concerns\HasResourcesLocation;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\FileGenerators\Concerns\CanCheckFileGenerationFlags;
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;
use Filament\Support\Facades\FilamentCli;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-page', aliases: [
    'filament:make-page',
    'filament:page',
])]
class MakePageCommand extends Command
{
    use CanAskForRelatedModel;
    use CanAskForRelatedResource;
    use CanAskForResource;
    use CanAskForSchema;
    use CanAskForViewLocation;
    use CanCheckFileGenerationFlags;
    use CanManipulateFiles;
    use HasCluster;
    use HasClusterPagesLocation;
    use HasPanel;
    use HasResourcesLocation;

    protected $description = 'Create a new Filament page class and view';

    protected $name = 'make:filament-page';

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
     * @var class-string<ResourcePage> | null
     */
    protected ?string $resourcePageType = null;

    protected string $pagesNamespace;

    protected string $pagesDirectory;

    public static bool $shouldCheckModelsForSoftDeletes = true;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:make-page',
        'filament:page',
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
                description: 'The name of the page to generate, optionally prefixed with directories',
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
                description: 'The cluster to create the page in',
            ),
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the page in',
            ),
            new InputOption(
                name: 'resource',
                shortcut: 'R',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The resource to create the page in',
            ),
            new InputOption(
                name: 'resource-namespace',
                shortcut: null,
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The namespace of the resource class, such as [' . app()->getNamespace() . 'Filament\\Resources]',
            ),
            new InputOption(
                name: 'type',
                shortcut: 'T',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The type of resource page to create',
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
            $this->configurePanel(question: 'Which panel would you like to create this page in?');
            $this->configureHasResource();
            $this->configureCluster();
            $this->configureResource();
            $this->configureResourcePageType();
            $this->configurePagesLocation();

            $this->configureLocation();

            $this->createCustomPage();
            $this->createResourceCustomPage();
            $this->createResourceCreatePage();
            $this->createResourceEditPage();
            $this->createResourceViewPage();
            $this->createResourceManageRelatedRecordsPage();
            $this->createView();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament page [{$this->fqn}] created successfully.");

        if (filled($this->resourceFqn)) {
            $this->components->info("Make sure to register the page in [{$this->resourceFqn}::getPages()].");
        } elseif (empty($this->panel->getPageNamespaces())) {
            $this->components->info('Make sure to register the page with [pages()] or discover it with [discoverPages()] in the panel service provider.');
        }

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the page name?',
            placeholder: 'ManageSettings',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureHasResource(): void
    {
        $this->hasResource = $this->option('resource') || confirm(
            label: 'Would you like to create this page in a resource?',
            default: false,
        );
    }

    protected function configureCluster(): void
    {
        if ($this->hasResource) {
            $this->configureClusterFqn(
                initialQuestion: 'Is the resource in a cluster?',
                question: 'Which cluster is the resource in?',
            );
        } else {
            $this->configureClusterFqn(
                initialQuestion: 'Would you like to create this page in a cluster?',
                question: 'Which cluster would you like to create this page in?',
            );
        }

        if (blank($this->clusterFqn)) {
            return;
        }

        $this->configureClusterPagesLocation();
        $this->configureClusterResourcesLocation();
    }

    protected function configureResource(): void
    {
        if (! $this->hasResource) {
            return;
        }

        $this->configureResourcesLocation(question: 'Which namespace would you like to search for resources in?');

        $this->resourceFqn = $this->askForResource(
            question: 'Which resource would you like to create this page in?',
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
            $this->pagesNamespace = (string) str($this->resourceFqn)
                ->beforeLast('\\')
                ->append('\\Pages');
            $this->pagesDirectory = (string) str((new ReflectionClass($this->resourceFqn))->getFileName())
                ->beforeLast(DIRECTORY_SEPARATOR)
                ->append('/Pages');

            return;
        }

        $this->pagesNamespace = "{$this->resourceFqn}\\Pages";
        $this->pagesDirectory = (string) str((new ReflectionClass($this->resourceFqn))->getFileName())
            ->beforeLast('.')
            ->append('/Pages');
    }

    protected function configureResourcePageType(): void
    {
        if (! $this->hasResource) {
            return;
        }

        $type = match ((string) str($this->option('type'))->slug()->replace('-', '')) {
            'custom' => ResourcePage::class,
            'create', 'createrecord' => CreateRecord::class,
            'edit', 'editrecord' => EditRecord::class,
            'view', 'viewrecord' => ViewRecord::class,
            'managerelated', 'related', 'relation', 'relationship', 'managerelatedrecords' => ManageRelatedRecords::class,
            default => $this->option('type'),
        };

        if (! class_exists($type)) {
            $type = select(
                label: 'Which type of resource page would you like to create?',
                options: [
                    ResourcePage::class => 'Custom',
                    CreateRecord::class => 'Create',
                    EditRecord::class => 'Edit',
                    ViewRecord::class => 'View',
                    ManageRelatedRecords::class => 'Manage relationship',
                ],
            );
        }

        $this->resourcePageType = $type;
    }

    protected function configurePagesLocation(): void
    {
        if (filled($this->resourceFqn)) {
            return;
        }

        if (filled($this->clusterFqn)) {
            return;
        }

        $directories = $this->panel->getPageDirectories();
        $namespaces = $this->panel->getPageNamespaces();

        foreach ($directories as $index => $directory) {
            if (str($directory)->startsWith(base_path('vendor'))) {
                unset($directories[$index]);
                unset($namespaces[$index]);
            }
        }

        if (count($namespaces) < 2) {
            $this->pagesNamespace = (Arr::first($namespaces) ?? app()->getNamespace() . 'Filament\\Pages');
            $this->pagesDirectory = (Arr::first($directories) ?? app_path('Filament/Pages/'));

            return;
        }

        $keyedNamespaces = array_combine(
            $namespaces,
            $namespaces,
        );

        $this->pagesNamespace = search(
            label: 'Which namespace would you like to create this page in?',
            options: function (?string $search) use ($keyedNamespaces): array {
                if (blank($search)) {
                    return $keyedNamespaces;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($keyedNamespaces, fn (string $namespace): bool => str($namespace)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
        );
        $this->pagesDirectory = $directories[array_search($this->pagesNamespace, $namespaces)];
    }

    protected function configureLocation(): void
    {
        $this->fqn = $this->pagesNamespace . '\\' . $this->fqnEnd;

        if ((! $this->hasResource) || ($this->resourcePageType === ResourcePage::class)) {
            $componentLocations = FilamentCli::getComponentLocations();

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
                        fn (Stringable $fqn) => $fqn->replaceFirst(app()->getNamespace(), ''),
                    )
                    ->replace('\\', '/')
                    ->explode('/')
                    ->map(Str::kebab(...))
                    ->implode('.'),
                question: 'Where would you like to create the Blade view for the page?',
                defaultNamespace: (count($matchingComponentLocationNamespaces) === 1)
                    ? $componentLocations[Arr::first($matchingComponentLocationNamespaces)]['viewNamespace'] ?? null
                    : null,
            );
        }
    }

    protected function createCustomPage(): void
    {
        if ($this->hasResource) {
            return;
        }

        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(CustomPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'view' => $this->view,
            'clusterFqn' => $this->clusterFqn,
        ]));
    }

    protected function createResourceCustomPage(): void
    {
        if ($this->resourcePageType !== ResourcePage::class) {
            return;
        }

        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        info('Most pages in a resource, such as the default Edit or View pages, have a record ID in their URL.');

        $this->writeFile($path, app(ResourceCustomPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'resourceFqn' => $this->resourceFqn,
            'view' => $this->view,
            'hasRecord' => confirm(
                label: 'Does this new page relate to a specific record in the resource like this?',
                default: false,
            ),
        ]));
    }

    protected function createResourceCreatePage(): void
    {
        if ($this->resourcePageType !== CreateRecord::class) {
            return;
        }

        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceCreateRecordPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'resourceFqn' => $this->resourceFqn,
        ]));
    }

    protected function createResourceEditPage(): void
    {
        if ($this->resourcePageType !== EditRecord::class) {
            return;
        }

        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceEditRecordPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'resourceFqn' => $this->resourceFqn,
            'hasViewOperation' => $this->resourceFqn::hasPage('view'),
            'isSoftDeletable' => confirm(
                label: 'Does the model use soft-deletes?',
                default: false,
            ),
        ]));
    }

    protected function createResourceViewPage(): void
    {
        if ($this->resourcePageType !== ViewRecord::class) {
            return;
        }

        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(ResourceViewRecordPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'resourceFqn' => $this->resourceFqn,
        ]));
    }

    protected function createResourceManageRelatedRecordsPage(): void
    {
        if ($this->resourcePageType !== ManageRelatedRecords::class) {
            return;
        }

        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $relationship = text(
            label: 'What is the relationship?',
            placeholder: 'members',
            required: true,
        );

        $hasViewOperation = false;
        $formSchemaFqn = null;
        $infolistSchemaFqn = null;
        $tableFqn = null;
        $recordTitleAttribute = null;
        $isGenerated = null;
        $relatedModelFqn = null;
        $isSoftDeletable = false;
        $relationshipType = null;

        $relatedResourceFqn = $this->askForRelatedResource();

        if (blank($relatedResourceFqn)) {
            $askForIsGeneratedIfNotAlready = function (?string $question = null) use (&$isGenerated): bool {
                return $isGenerated ??= confirm(
                    label: $question ?? 'Should the page be generated from the current database columns?',
                    default: false,
                );
            };

            $askForRelatedModelFqnIfNotAlready = function () use (&$relatedModelFqn, $relationship): string {
                if (filled($relatedModelFqn)) {
                    return $relatedModelFqn;
                }

                $resourceModelFqn = $this->resourceFqn::getModel();

                if (
                    class_exists($resourceModelFqn) &&
                    method_exists($resourceModelFqn, $relationship) &&
                    (($relationshipInstance = app($resourceModelFqn)->{$relationship}()) instanceof Relation)
                ) {
                    return $relatedModelFqn = $relationshipInstance->getRelated()::class;
                }

                return $relatedModelFqn = $this->askForRelatedModel($relationship);
            };

            $askForRecordTitleAttributeIfNotAlready = function () use (&$recordTitleAttribute): string {
                if (filled($recordTitleAttribute)) {
                    return $recordTitleAttribute;
                }

                info('The "title attribute" is used to label each record in the UI.');

                return $recordTitleAttribute = text(
                    label: 'What is the title attribute for this model?',
                    placeholder: 'name',
                    required: true,
                );
            };

            if (! $this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS)) {
                $formSchemaFqn = $this->askForSchema(
                    intialQuestion: 'Should an existing form schema class be used?',
                    question: 'Which form schema class would you like to use?',
                    questionPlaceholder: app()->getNamespace() . 'Filament\\Resources\\Users\\Schemas\\UserForm',
                );
            }

            if (blank($formSchemaFqn)) {
                $askForIsGeneratedIfNotAlready()
                    ? $askForRelatedModelFqnIfNotAlready()
                    : $askForRecordTitleAttributeIfNotAlready();
            }

            if (confirm(
                'Would you like to generate a read-only view modal for the table?',
                default: false,
            )) {
                $hasViewOperation = true;

                if (! $this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS)) {
                    $infolistSchemaFqn = $this->askForSchema(
                        intialQuestion: 'Would you like to use an existing infolist schema class?',
                        question: 'Which infolist schema class would you like to use?',
                        questionPlaceholder: app()->getNamespace() . 'Filament\\Resources\\Users\\Schemas\\UserInfolist',
                    );
                }

                if (blank($infolistSchemaFqn)) {
                    $askForRecordTitleAttributeIfNotAlready();
                }
            }

            if ($this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES)) {
                $askForIsGeneratedIfNotAlready()
                    ? $askForRelatedModelFqnIfNotAlready()
                    : $askForRecordTitleAttributeIfNotAlready();
            } else {
                $tableFqn = $this->askForSchema(
                    intialQuestion: 'Would you like to use an existing table class?',
                    question: 'Which table class would you like to use?',
                    questionPlaceholder: app()->getNamespace() . 'Filament\\Resources\\Users\\Tables\\UsersTable',
                );
            }

            if (blank($tableFqn)) {
                $askForRecordTitleAttributeIfNotAlready();

                $askForIsGeneratedIfNotAlready(
                    question: 'Should the table columns be generated from the current database columns?',
                ) && $askForRelatedModelFqnIfNotAlready();

                $isSoftDeletable = (filled($relatedModelFqn) && static::$shouldCheckModelsForSoftDeletes && class_exists($relatedModelFqn))
                    ? in_array(SoftDeletes::class, class_uses_recursive($relatedModelFqn))
                    : confirm(
                        label: 'Does the related model use soft-deletes?',
                        default: false,
                    );

                $relationshipType = select(
                    label: 'What type of relationship is this?',
                    options: [
                        HasMany::class => 'HasMany',
                        BelongsToMany::class => 'BelongsToMany',
                        MorphMany::class => 'MorphMany',
                        MorphToMany::class => 'MorphToMany',
                        'other' => 'Other',
                    ],
                );

                if ($relationshipType === 'other') {
                    $relationshipType = null;
                }
            }
        }

        $this->writeFile($path, app(ResourceManageRelatedRecordsPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'resourceFqn' => $this->resourceFqn,
            'relationship' => $relationship,
            'relatedResourceFqn' => $relatedResourceFqn,
            'hasViewOperation' => $hasViewOperation,
            'formSchemaFqn' => $formSchemaFqn,
            'infolistSchemaFqn' => $infolistSchemaFqn,
            'tableFqn' => $tableFqn,
            'recordTitleAttribute' => $recordTitleAttribute,
            'isGenerated' => $isGenerated ?? false,
            'relatedModelFqn' => $relatedModelFqn,
            'isSoftDeletable' => $isSoftDeletable,
            'relationshipType' => $relationshipType,
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

        $this->copyStubToApp('PageView', $this->viewPath);
    }
}
