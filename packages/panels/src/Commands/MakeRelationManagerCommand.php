<?php

namespace Filament\Commands;

use Filament\Commands\FileGenerators\Resources\RelationManagerClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForRelatedModel;
use Filament\Support\Commands\Concerns\CanAskForRelatedResource;
use Filament\Support\Commands\Concerns\CanAskForResource;
use Filament\Support\Commands\Concerns\CanAskForSchema;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasCluster;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Concerns\HasResourcesLocation;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\FileGenerators\Concerns\CanCheckFileGenerationFlags;
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-relation-manager', aliases: [
    'filament:make-relation-manager',
    'filament:relation-manager',
])]
class MakeRelationManagerCommand extends Command
{
    use CanAskForRelatedModel;
    use CanAskForRelatedResource;
    use CanAskForResource;
    use CanAskForSchema;
    use CanCheckFileGenerationFlags;
    use CanManipulateFiles;
    use HasCluster;
    use HasPanel;
    use HasResourcesLocation;

    protected $description = 'Create a new Filament relation manager class for a resource';

    protected $name = 'make:filament-relation-manager';

    protected string $fqn;

    protected string $path;

    /**
     * @var class-string
     */
    protected string $resourceFqn;

    protected string $relationship;

    /**
     * @var ?class-string
     */
    protected ?string $relatedResourceFqn = null;

    /**
     * @var ?class-string<Model>
     */
    protected ?string $relatedModelFqn = null;

    protected bool $hasViewOperation = false;

    /**
     * @var ?class-string
     */
    protected ?string $formSchemaFqn = null;

    /**
     * @var ?class-string
     */
    protected ?string $infolistSchemaFqn = null;

    /**
     * @var ?class-string
     */
    protected ?string $tableFqn = null;

    protected ?string $recordTitleAttribute = null;

    protected bool $isGenerated;

    protected bool $isSoftDeletable;

    /**
     * @var ?class-string<Relation>
     */
    protected ?string $relationshipType = null;

    public static bool $shouldCheckModelsForSoftDeletes = true;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:make-relation-manager',
        'filament:relation-manager',
    ];

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'resource',
                mode: InputArgument::OPTIONAL,
                description: 'The resource to create the relation manager in',
            ),
            new InputArgument(
                name: 'relationship',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the relationship to manage',
            ),
            new InputArgument(
                name: 'recordTitleAttribute',
                mode: InputArgument::OPTIONAL,
                description: 'The title attribute, used to label each record in the UI',
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
                name: 'associate',
                mode: InputOption::VALUE_NONE,
                description: 'Include associate actions in the table for `HasMany` and `MorphMany` relationships',
            ),
            new InputOption(
                name: 'attach',
                mode: InputOption::VALUE_NONE,
                description: 'Include attach actions in the table for `BelongsToMany` and `MorphToMany` relationships',
            ),
            new InputOption(
                name: 'cluster',
                shortcut: 'C',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The cluster that the resource belongs to',
            ),
            new InputOption(
                name: 'form-schema',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The fully-qualified class name of the form schema class to use',
            ),
            new InputOption(
                name: 'generate',
                shortcut: 'G',
                mode: InputOption::VALUE_NONE,
                description: 'Generate the form schema and table columns from the current database columns',
            ),
            new InputOption(
                name: 'infolist-schema',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The fully-qualified class name of the infolist schema class to use',
            ),
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the relation manager in',
            ),
            new InputOption(
                name: 'record-title-attribute',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The title attribute, used to label each record in the UI',
            ),
            new InputOption(
                name: 'related-model',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The fully-qualified class name of the related model',
            ),
            new InputOption(
                name: 'related-resource',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The fully-qualified class name of the related resource',
            ),
            new InputOption(
                name: 'resource-namespace',
                shortcut: null,
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The namespace of the resource class, such as [' . app()->getNamespace() . 'Filament\\Resources]',
            ),
            new InputOption(
                name: 'soft-deletes',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Indicate if the model uses soft-deletes',
            ),
            new InputOption(
                name: 'table',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The fully-qualified class name of the table class to use',
            ),
            new InputOption(
                name: 'view',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Generate a view modal for the relation manager',
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
            $this->configurePanel(question: 'Which panel would you like to create this relation manager in?');
            $this->configureResource();
            $this->configureRelationship();
            $this->configureRelatedResource();

            if (blank($this->relatedResourceFqn)) {
                $this->configureFormSchemaFqn();

                if (blank($this->formSchemaFqn)) {
                    $this->configureIsGeneratedIfNotAlready();

                    $this->isGenerated
                        ? $this->configureRelatedModelFqnIfNotAlready()
                        : $this->configureRecordTitleAttributeIfNotAlready();
                }

                $this->configureHasViewOperation();

                if ($this->hasViewOperation) {
                    $this->configureInfolistSchemaFqn();

                    if (blank($this->infolistSchemaFqn)) {
                        $this->configureRecordTitleAttributeIfNotAlready();
                    }
                }

                if ($this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES)) {
                    $this->configureIsGeneratedIfNotAlready();

                    $this->isGenerated
                        ? $this->configureRelatedModelFqnIfNotAlready()
                        : $this->configureRecordTitleAttributeIfNotAlready();
                } else {
                    $this->configureTableFqn();
                }

                if (blank($this->tableFqn)) {
                    $this->configureRecordTitleAttributeIfNotAlready();

                    $this->configureIsGeneratedIfNotAlready(
                        question: 'Should the table columns be generated from the current database columns?',
                    );

                    if ($this->isGenerated) {
                        $this->configureRelatedModelFqnIfNotAlready();
                    }

                    $this->configureIsSoftDeletable();

                    $this->configureRelationshipType();
                }
            }

            $this->configureLocation();

            $this->createRelationManager();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament relation manager [{$this->fqn}] created successfully.");

        $this->components->info("Make sure to register the relation in [{$this->resourceFqn}::getRelations()].");

        return static::SUCCESS;
    }

    protected function configureResource(): void
    {
        $this->configureClusterFqn(
            initialQuestion: 'Is the resource in a cluster?',
            question: 'Which cluster is the resource in?',
        );

        if (filled($this->clusterFqn)) {
            $this->configureClusterResourcesLocation();
        } else {
            $this->configureResourcesLocation(question: 'Which namespace is the resource in?');
        }

        $this->resourceFqn = $this->askForResource(
            question: 'Which resource would you like to create this relation manager in?',
            initialResource: $this->argument('resource'),
        );
    }

    protected function configureRelationship(): void
    {
        $relationships = class_exists($model = $this->resourceFqn::getModel())
            ? collect((new ReflectionClass($model))->getMethods())
                ->filter(fn (ReflectionMethod $method): bool => $method->getNumberOfParameters() === 0)
                ->filter(function (ReflectionMethod $method): bool {
                    $returnType = (string) $method->getReturnType();

                    if (! class_exists($returnType)) {
                        return false;
                    }

                    foreach ([
                        HasMany::class,
                        MorphMany::class,
                        BelongsToMany::class,
                        MorphToMany::class,
                    ] as $relationType) {
                        if (is_subclass_of($returnType, $relationType)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->map(fn (ReflectionMethod $method): string => $method->getName())
                ->all()
            : [];

        if (count($relationships)) {
            $this->relationship = $this->argument('relationship') ?? suggest(
                label: 'What is the relationship?',
                options: $relationships,
                placeholder: Arr::first($relationships),
                required: true,
            );

            if (method_exists($model, $this->relationship)) {
                $this->relatedModelFqn = app($model)->{$this->relationship}()->getRelated()::class;
            }

            return;
        }

        $this->relationship = $this->argument('relationship') ?? text(
            label: 'What is the relationship?',
            placeholder: 'members',
            required: true,
        );
    }

    protected function configureRelatedResource(): void
    {
        $relatedResource = $this->option('related-resource');

        $this->relatedResourceFqn = filled($relatedResource) && class_exists($relatedResource)
            ? $relatedResource
            : $this->askForRelatedResource();

        if (filled($this->relatedResourceFqn)) {
            $this->relatedModelFqn ??= $this->relatedResourceFqn::getModel();
        }
    }

    protected function configureFormSchemaFqn(): void
    {
        if ($this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS)) {
            return;
        }

        $formSchema = $this->option('form-schema');

        $this->formSchemaFqn = filled($formSchema) && class_exists($formSchema)
            ? $formSchema
            : $this->askForSchema(
                intialQuestion: 'Would you like to use an existing form schema class?',
                question: 'Which form schema class would you like to use?',
                questionPlaceholder: app()->getNamespace() . 'Filament\\Resources\\Users\\Schemas\\UserForm',
            );
    }

    protected function configureIsGeneratedIfNotAlready(?string $question = null): void
    {
        $this->isGenerated ??= $this->option('generate') || confirm(
            label: $question ?? 'Should the configuration be generated from the current database columns?',
            default: false,
        );
    }

    protected function configureRelatedModelFqnIfNotAlready(): void
    {
        if (filled($this->relatedModelFqn)) {
            return;
        }

        $relatedModel = $this->option('related-model');

        $this->relatedModelFqn = filled($relatedModel) && class_exists($relatedModel)
            ? $relatedModel
            : $this->askForRelatedModel($this->relationship);
    }

    protected function configureRecordTitleAttributeIfNotAlready(): void
    {
        $this->recordTitleAttribute ??= $this->option('record-title-attribute') ?? $this->argument('recordTitleAttribute');

        if (filled($this->recordTitleAttribute)) {
            return;
        }

        info('The "title attribute" is used to label each record in the UI.');

        $this->recordTitleAttribute = text(
            label: 'What is the title attribute for this model?',
            placeholder: 'name',
            required: true,
        );
    }

    protected function configureHasViewOperation(): void
    {
        $this->hasViewOperation = $this->option('view') || confirm(
            label: 'Should there be a read-only "view" modal on the relation manager?',
            default: false,
        );
    }

    protected function configureInfolistSchemaFqn(): void
    {
        if ($this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS)) {
            return;
        }

        $infolistSchema = $this->option('infolist-schema');

        $this->infolistSchemaFqn = filled($infolistSchema) && class_exists($infolistSchema)
            ? $infolistSchema
            : $this->askForSchema(
                intialQuestion: 'Would you like to use an existing infolist schema class?',
                question: 'Which infolist schema class would you like to use?',
                questionPlaceholder: app()->getNamespace() . 'Filament\\Resources\\Users\\Schemas\\UserInfolist',
            );
    }

    protected function configureTableFqn(): void
    {
        if ($this->hasFileGenerationFlag(FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES)) {
            return;
        }

        $table = $this->option('table');

        $this->tableFqn = filled($table) && class_exists($table)
            ? $table
            : $this->askForSchema(
                intialQuestion: 'Would you like to use an existing table class?',
                question: 'Which table class would you like to use?',
                questionPlaceholder: app()->getNamespace() . 'Filament\\Resources\\Users\\Tables\\UsersTable',
            );
    }

    protected function configureIsSoftDeletable(): void
    {
        $this->isSoftDeletable = $this->option('soft-deletes') || ((static::$shouldCheckModelsForSoftDeletes && filled($this->relatedModelFqn))
            ? in_array(SoftDeletes::class, class_uses_recursive($this->relatedModelFqn))
            : confirm(
                label: 'Does the model use soft-deletes?',
                default: false,
            ));
    }

    protected function configureRelationshipType(): void
    {
        if ($this->option('associate')) {
            $this->relationshipType = HasMany::class;

            return;
        }

        if ($this->option('attach')) {
            $this->relationshipType = BelongsToMany::class;

            return;
        }

        $this->relationshipType = select(
            label: 'What type of relationship is this?',
            options: [
                HasMany::class => 'HasMany',
                BelongsToMany::class => 'BelongsToMany',
                MorphMany::class => 'MorphMany',
                MorphToMany::class => 'MorphToMany',
                'other' => 'Other',
            ],
        );

        if ($this->relationshipType === 'other') {
            $this->relationshipType = null;
        }
    }

    protected function configureLocation(): void
    {
        $basename = (string) str($this->relationship)
            ->studly()
            ->append('RelationManager');

        $pluralResourceBasenameBeforeResource = (string) str($this->resourceFqn)
            ->classBasename()
            ->beforeLast('Resource')
            ->plural();

        $resourceNamespacePartBeforeBasename = (string) str($this->resourceFqn)
            ->beforeLast('\\')
            ->classBasename();

        if ($pluralResourceBasenameBeforeResource === $resourceNamespacePartBeforeBasename) {
            $this->fqn = (string) str($this->resourceFqn)
                ->beforeLast('\\')
                ->append("\\RelationManagers\\{$basename}");
            $this->path = (string) str((new ReflectionClass($this->resourceFqn))->getFileName())
                ->beforeLast(DIRECTORY_SEPARATOR)
                ->append("/RelationManagers/{$basename}.php");

            return;
        }

        $this->fqn = "{$this->resourceFqn}\\RelationManagers\\{$basename}";
        $this->path = (string) str((new ReflectionClass($this->resourceFqn))->getFileName())
            ->beforeLast('.')
            ->append("/RelationManagers/{$basename}.php");
    }

    protected function createRelationManager(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(RelationManagerClassGenerator::class, [
            'fqn' => $this->fqn,
            'resourceFqn' => $this->resourceFqn,
            'relationship' => $this->relationship,
            'relatedResourceFqn' => $this->relatedResourceFqn,
            'hasViewOperation' => $this->hasViewOperation,
            'formSchemaFqn' => $this->formSchemaFqn,
            'infolistSchemaFqn' => $this->infolistSchemaFqn,
            'tableFqn' => $this->tableFqn,
            'recordTitleAttribute' => $this->recordTitleAttribute,
            'isGenerated' => $this->isGenerated ?? false,
            'relatedModelFqn' => $this->relatedModelFqn,
            'isSoftDeletable' => $this->isSoftDeletable ?? false,
            'relationshipType' => $this->relationshipType,
        ]));
    }
}
