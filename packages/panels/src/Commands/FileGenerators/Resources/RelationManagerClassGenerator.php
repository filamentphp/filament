<?php

namespace Filament\Commands\FileGenerators\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceForms;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceInfolists;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

class RelationManagerClassGenerator extends ClassGenerator
{
    use CanGenerateResourceForms;
    use CanGenerateResourceInfolists;
    use CanGenerateResourceTables;
    use CanReadModelSchemas;

    /**
     * @param  class-string  $resourceFqn
     * @param  ?class-string  $relatedResourceFqn
     * @param  ?class-string  $formSchemaFqn
     * @param  ?class-string  $infolistSchemaFqn
     * @param  ?class-string  $tableFqn
     * @param  ?class-string<Model>  $relatedModelFqn
     * @param  ?class-string<Relation>  $relationshipType
     */
    final public function __construct(
        protected string $fqn,
        protected string $resourceFqn,
        protected string $relationship,
        protected ?string $relatedResourceFqn,
        protected bool $hasViewOperation,
        protected ?string $formSchemaFqn,
        protected ?string $infolistSchemaFqn,
        protected ?string $tableFqn,
        protected ?string $recordTitleAttribute,
        protected bool $isGenerated,
        protected ?string $relatedModelFqn,
        protected bool $isSoftDeletable,
        protected ?string $relationshipType,
    ) {}

    public function getNamespace(): string
    {
        return $this->extractNamespace($this->getFqn());
    }

    /**
     * @return array<string>
     */
    public function getImports(): array
    {
        return [
            $this->getExtends(),
            ...(filled($relatedResourceFqn = $this->getRelatedResourceFqn())
                ? [$relatedResourceFqn]
                : [
                    Schema::class,
                    ...($this->hasPartialImports() ? [
                        ...(blank($this->getTableFqn()) ? ['Filament\Actions', 'Filament\Tables'] : []),
                        ...(blank($this->getFormSchemaFqn()) ? ['Filament\Forms'] : []),
                        ...($this->hasViewOperation() && blank($this->getInfolistSchemaFqn())) ? ['Filament\Infolists'] : [],
                    ] : [
                        ...(filled($this->getTableFqn()) ? [$this->getTableFqn()] : []),
                        ...(filled($this->getFormSchemaFqn()) ? [$this->getFormSchemaFqn()] : []),
                        ...(filled($this->getInfolistSchemaFqn()) ? [$this->getInfolistSchemaFqn()] : []),
                    ]),
                ]),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return RelationManager::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addRelationshipPropertyToClass($class);
        $this->addRelatedResourcePropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addFormMethodToClass($class);
        $this->addInfolistMethodToClass($class);
        $this->addTableMethodToClass($class);
    }

    protected function addRelationshipPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('relationship', $this->getRelationship())
            ->setProtected()
            ->setStatic()
            ->setType('string');
        $this->configureRelationshipProperty($property);
    }

    protected function configureRelationshipProperty(Property $property): void {}

    protected function addRelatedResourcePropertyToClass(ClassType $class): void
    {
        if (! $this->hasRelatedResource()) {
            return;
        }

        $property = $class->addProperty('relatedResource', new Literal("{$this->simplifyFqn($this->getRelatedResourceFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureRelatedResourceProperty($property);
    }

    protected function configureRelatedResourceProperty(Property $property): void {}

    protected function addFormMethodToClass(ClassType $class): void
    {
        if ($this->hasRelatedResource()) {
            return;
        }

        $formSchemaFqn = $this->getFormSchemaFqn();

        $methodBody = filled($formSchemaFqn)
            ? <<<PHP
                return {$this->simplifyFqn($formSchemaFqn)}::configure(\$schema);
                PHP
            : $this->generateFormMethodBody($this->getRelatedModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate()));

        $method = $class->addMethod('form')
            ->setPublic()
            ->setReturnType(Schema::class)
            ->setBody($methodBody);
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureFormMethod($method);
    }

    protected function configureFormMethod(Method $method): void {}

    protected function addInfolistMethodToClass(ClassType $class): void
    {
        if (! $this->hasViewOperation()) {
            return;
        }

        if ($this->hasRelatedResource()) {
            return;
        }

        $infolistSchemaFqn = $this->getInfolistSchemaFqn();

        $methodBody = filled($infolistSchemaFqn)
            ? <<<PHP
                return {$this->simplifyFqn($infolistSchemaFqn)}::configure(\$schema);
                PHP
            : $this->generateInfolistMethodBody($this->getRelatedModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate()));

        $method = $class->addMethod('infolist')
            ->setPublic()
            ->setReturnType(Schema::class)
            ->setBody($methodBody);
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureInfolistMethod($method);
    }

    protected function configureInfolistMethod(Method $method): void {}

    protected function addTableMethodToClass(ClassType $class): void
    {
        $relatedResource = $this->getRelatedResourceFqn();
        $tableFqn = $this->getTableFqn();

        $hasExtraTableConfiguration = $this->hasAttachTableActions() || $this->hasAssociateTableActions();

        if ($relatedResource && blank($headerActionsOutput = $this->outputTableHeaderActions())) {
            // If the related resource is set and there are no table header actions to add, we don't need
            // to generate the table method since it will be inherited from the related resource.
            return;
        }

        if (filled($tableFqn) && (! $hasExtraTableConfiguration)) {
            // If a table class is provided and there are no extra actions to add,
            // we can just delegate to the table class without generating the method.
            $this->namespace->addUse(Table::class);

            $methodBody = <<<PHP
                return {$this->simplifyFqn($tableFqn)}::configure(\$table);
                PHP;

            $method = $class->addMethod('table')
                ->setPublic()
                ->setReturnType(Table::class)
                ->setBody($methodBody);
            $method->addParameter('table')
                ->setType(Table::class);

            $this->configureTableMethod($method);

            return;
        }

        $this->namespace->addUse(Table::class);

        if ($relatedResource) {
            $methodBody = <<<PHP
                return \$table
                    ->headerActions([
                        {$headerActionsOutput}
                    ]);
                PHP;
        } elseif (filled($tableFqn)) {
            $methodBody = $this->generateTableMethodBodyWithTableClass($tableFqn);
        } else {
            $methodBody = $this->generateTableMethodBody($this->getRelatedModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate()));
        }

        $method = $class->addMethod('table')
            ->setPublic()
            ->setReturnType(Table::class)
            ->setBody($methodBody);
        $method->addParameter('table')
            ->setType(Table::class);

        $this->configureTableMethod($method);
    }

    protected function generateTableMethodBodyWithTableClass(string $tableFqn): string
    {
        $this->importUnlessPartial(BulkActionGroup::class);

        $recordTitleAttributeOutput = '';

        if (filled($recordTitleAttribute = $this->getRecordTitleAttribute())) {
            $recordTitleAttributeOutput = new Literal(<<<'PHP'

                ->recordTitleAttribute(?)
            PHP, [$recordTitleAttribute]);
        }

        $headerActionsOutput = '';

        if (filled($headerActions = $this->outputTableHeaderActions())) {
            $headerActionsOutput = <<<PHP

                ->headerActions([
                    {$headerActions}
                ])
            PHP;
        }

        return <<<PHP
            return {$this->simplifyFqn($tableFqn)}::configure(\$table){$recordTitleAttributeOutput}{$headerActionsOutput}
                ->recordActions([
                    {$this->outputTableActions()}
                ])
                ->toolbarActions([
                    {$this->simplifyFqn(BulkActionGroup::class)}::make([
                        {$this->outputTableMethodBulkActions()}
                    ]),
                ]);
            PHP;
    }

    public function hasCreateTableAction(): bool
    {
        return true;
    }

    public function hasAssociateTableActions(): bool
    {
        return in_array($this->getRelationshipType(), [HasMany::class, MorphMany::class]);
    }

    public function hasAttachTableActions(): bool
    {
        return in_array($this->getRelationshipType(), [BelongsToMany::class, MorphToMany::class]);
    }

    public function hasDeleteTableActions(): bool
    {
        return true;
    }

    public function hasTableModifyQueryForSoftDeletes(): bool
    {
        return true;
    }

    protected function configureTableMethod(Method $method): void {}

    public function getForeignKeyColumnToNotGenerate(): ?string
    {
        if (! class_exists($this->getResourceFqn())) {
            return null;
        }

        $model = $this->getResourceFqn()::getModel();

        if (! class_exists($model)) {
            return null;
        }

        $modelInstance = app($model);
        $relationshipName = $this->getRelationship();

        if (! method_exists($modelInstance, $relationshipName)) {
            return null;
        }

        $relationship = $modelInstance->{$relationshipName}();

        if (! ($relationship instanceof HasMany)) {
            return null;
        }

        return $relationship->getForeignKeyName();
    }

    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * @return class-string
     */
    public function getResourceFqn(): string
    {
        return $this->resourceFqn;
    }

    public function getRelationship(): string
    {
        return $this->relationship;
    }

    /**
     * @return ?class-string
     */
    public function getRelatedResourceFqn(): ?string
    {
        return $this->relatedResourceFqn;
    }

    public function hasRelatedResource(): bool
    {
        return filled($this->getRelatedResourceFqn());
    }

    public function hasViewOperation(): bool
    {
        return $this->hasViewOperation;
    }

    /**
     * @return ?class-string
     */
    public function getFormSchemaFqn(): ?string
    {
        return $this->formSchemaFqn;
    }

    /**
     * @return ?class-string
     */
    public function getInfolistSchemaFqn(): ?string
    {
        return $this->infolistSchemaFqn;
    }

    /**
     * @return ?class-string
     */
    public function getTableFqn(): ?string
    {
        return $this->tableFqn;
    }

    public function getRecordTitleAttribute(): ?string
    {
        return $this->recordTitleAttribute;
    }

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    /**
     * @return ?class-string<Model>
     */
    public function getRelatedModelFqn(): ?string
    {
        return $this->relatedModelFqn;
    }

    public function isSoftDeletable(): bool
    {
        return $this->isSoftDeletable;
    }

    /**
     * @return ?class-string<Relation>
     */
    public function getRelationshipType(): ?string
    {
        return $this->relationshipType;
    }
}
