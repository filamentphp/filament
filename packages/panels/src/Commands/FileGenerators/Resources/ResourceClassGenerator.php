<?php

namespace Filament\Commands\FileGenerators\Resources;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceForms;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceInfolists;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

class ResourceClassGenerator extends ClassGenerator
{
    use CanGenerateResourceForms;
    use CanGenerateResourceInfolists;
    use CanGenerateResourceTables;
    use CanReadModelSchemas;

    /**
     * @param  class-string<Model>  $modelFqn
     * @param  ?class-string  $formSchemaFqn
     * @param  ?class-string  $infolistSchemaFqn
     * @param  ?class-string  $tableFqn
     * @param  ?class-string<Cluster>  $clusterFqn
     * @param  ?class-string  $parentResourceFqn
     * @param array<string, array{
     *     class: class-string<Page>,
     *     path: string,
     * }> $pageRoutes
     */
    final public function __construct(
        protected string $fqn,
        protected string $modelFqn,
        protected array $pageRoutes,
        protected ?string $formSchemaFqn,
        protected ?string $infolistSchemaFqn,
        protected ?string $tableFqn,
        protected ?string $clusterFqn,
        protected ?string $parentResourceFqn,
        protected ?string $recordTitleAttribute,
        protected bool $hasViewOperation,
        protected bool $isGenerated,
        protected bool $isSoftDeletable,
        protected bool $isSimple,
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
            Resource::class,
            Schema::class,
            Table::class,
            ...(($this->getModelBasename() === 'Resource') ? [$this->getModelFqn() => 'ResourceModel'] : [$this->getModelFqn()]),
            ...($this->hasCluster() ? (($this->getClusterBasename() === 'Resource') ? [$this->getClusterFqn() => 'ResourceCluster'] : [$this->getClusterFqn()]) : []),
            ...($this->hasParentResource() ? [$this->getParentResourceFqn()] : []),
            ...($this->isSoftDeletable() ? [Builder::class, SoftDeletingScope::class] : []),
            ...$this->getPagesImports(),
            ...($this->hasPartialImports() ? [
                ...(blank($this->getTableFqn()) ? ['Filament\Actions', 'Filament\Tables'] : []),
                ...(blank($this->getFormSchemaFqn()) ? ['Filament\Forms'] : []),
                ...($this->hasViewOperation() && blank($this->getInfolistSchemaFqn())) ? ['Filament\Infolists'] : [],
            ] : [
                ...(filled($this->getTableFqn()) ? [$this->getTableFqn()] : []),
                ...(filled($this->getFormSchemaFqn()) ? [$this->getFormSchemaFqn()] : []),
                ...(filled($this->getInfolistSchemaFqn()) ? [$this->getInfolistSchemaFqn()] : []),
            ]),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Resource::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addModelPropertyToClass($class);
        $this->addNavigationIconPropertyToClass($class);
        $this->addClusterPropertyToClass($class);
        $this->addParentResourcePropertyToClass($class);
        $this->addRecordTitleAttributePropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addFormMethodToClass($class);
        $this->addInfolistMethodToClass($class);
        $this->addTableMethodToClass($class);
        $this->addGetRelationsMethodToClass($class);
        $this->addGetPagesMethodToClass($class);
        $this->addGetRecordRouteBindingEloquentQueryMethodToClass($class);
    }

    protected function addModelPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('model', new Literal("{$this->simplifyFqn($this->getModelFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureModelProperty($property);
    }

    protected function configureModelProperty(Property $property): void {}

    protected function addNavigationIconPropertyToClass(ClassType $class): void
    {
        $this->namespace->addUse(BackedEnum::class);
        $this->namespace->addUse(Heroicon::class);

        $property = $class->addProperty('navigationIcon', new Literal('Heroicon::OutlinedRectangleStack'))
            ->setProtected()
            ->setStatic()
            ->setType('string|BackedEnum|null');
        $this->configureNavigationIconProperty($property);
    }

    protected function configureNavigationIconProperty(Property $property): void {}

    protected function addClusterPropertyToClass(ClassType $class): void
    {
        if (! $this->hasCluster()) {
            return;
        }

        $property = $class->addProperty('cluster', new Literal("{$this->simplifyFqn($this->getClusterFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureClusterProperty($property);
    }

    protected function configureClusterProperty(Property $property): void {}

    protected function addParentResourcePropertyToClass(ClassType $class): void
    {
        if (! $this->hasParentResource()) {
            return;
        }

        $property = $class->addProperty('parentResource', new Literal("{$this->simplifyFqn($this->getParentResourceFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureParentResourceProperty($property);
    }

    protected function configureParentResourceProperty(Property $property): void {}

    protected function addRecordTitleAttributePropertyToClass(ClassType $class): void
    {
        $recordTitleAttribute = $this->getRecordTitleAttribute();

        if (blank($recordTitleAttribute)) {
            return;
        }

        $property = $class->addProperty('recordTitleAttribute', $recordTitleAttribute)
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureRecordTitleAttributeProperty($property);
    }

    protected function configureRecordTitleAttributeProperty(Property $property): void {}

    protected function addFormMethodToClass(ClassType $class): void
    {
        $formSchemaFqn = $this->getFormSchemaFqn();

        $methodBody = filled($formSchemaFqn)
            ? <<<PHP
                return {$this->simplifyFqn($formSchemaFqn)}::configure(\$schema);
                PHP
            : $this->generateFormMethodBody($this->getModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate()));

        $method = $class->addMethod('form')
            ->setPublic()
            ->setStatic()
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

        $infolistSchemaFqn = $this->getInfolistSchemaFqn();

        $methodBody = filled($infolistSchemaFqn)
            ? <<<PHP
                return {$this->simplifyFqn($infolistSchemaFqn)}::configure(\$schema);
                PHP
            : $this->generateInfolistMethodBody($this->getModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate()));

        $method = $class->addMethod('infolist')
            ->setPublic()
            ->setStatic()
            ->setReturnType(Schema::class)
            ->setBody($methodBody);
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureInfolistMethod($method);
    }

    protected function configureInfolistMethod(Method $method): void {}

    protected function addTableMethodToClass(ClassType $class): void
    {
        $tableFqn = $this->getTableFqn();

        $methodBody = filled($tableFqn)
            ? <<<PHP
                return {$this->simplifyFqn($tableFqn)}::configure(\$table);
                PHP
            : $this->generateTableMethodBody($this->getModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate()));

        $method = $class->addMethod('table')
            ->setPublic()
            ->setStatic()
            ->setReturnType(Table::class)
            ->setBody($methodBody);
        $method->addParameter('table')
            ->setType(Table::class);

        $this->configureTableMethod($method);
    }

    public function getForeignKeyColumnToNotGenerate(): ?string
    {
        if (! class_exists($this->getParentResourceFqn())) {
            return null;
        }

        $model = $this->getParentResourceFqn()::getModel();

        if (! class_exists($model)) {
            return null;
        }

        $modelInstance = app($model);
        $relationshipName = (string) str($this->getModelBasename())->plural()->camel();

        if (! method_exists($modelInstance, $relationshipName)) {
            return null;
        }

        $relationship = $modelInstance->{$relationshipName}();

        if (! ($relationship instanceof HasMany)) {
            return null;
        }

        return $relationship->getForeignKeyName();
    }

    protected function configureTableMethod(Method $method): void {}

    protected function addGetRelationsMethodToClass(ClassType $class): void
    {
        if ($this->isSimple()) {
            return;
        }

        $method = $class->addMethod('getRelations')
            ->setPublic()
            ->setStatic()
            ->setReturnType('array')
            ->setBody(
                <<<'PHP'
                return [
                    //
                ];
                PHP
            );

        $this->configureGetRelationsMethod($method);
    }

    protected function configureGetRelationsMethod(Method $method): void {}

    protected function addGetPagesMethodToClass(ClassType $class): void
    {
        $pageRoutes = $this->getPageRoutes();

        $pages = array_map(
            fn (array $page, string $routeName): string => (string) new Literal("? => {$this->simplifyFqn($page['class'])}::route(?),", [
                $routeName,
                $page['path'],
            ]),
            $pageRoutes,
            array_keys($pageRoutes),
        );

        $pagesOutput = implode(PHP_EOL . '    ', $pages);

        $method = $class->addMethod('getPages')
            ->setPublic()
            ->setStatic()
            ->setReturnType('array')
            ->setBody(
                <<<PHP
                return [
                    {$pagesOutput}
                ];
                PHP
            );

        $this->configureGetPagesMethod($method);
    }

    protected function configureGetPagesMethod(Method $method): void {}

    protected function addGetRecordRouteBindingEloquentQueryMethodToClass(ClassType $class): void
    {
        if (! $this->isSoftDeletable()) {
            return;
        }

        $method = $class->addMethod('getRecordRouteBindingEloquentQuery')
            ->setPublic()
            ->setStatic()
            ->setReturnType(Builder::class)
            ->setBody(
                <<<PHP
                return parent::getRecordRouteBindingEloquentQuery()
                    ->withoutGlobalScopes([
                        {$this->simplifyFqn(SoftDeletingScope::class)}::class,
                    ]);
                PHP
            );
        $this->configureGetRecordRouteBindingEloquentQueryMethod($method);
    }

    protected function configureGetRecordRouteBindingEloquentQueryMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getModelBasename(): string
    {
        return class_basename($this->getModelFqn());
    }

    /**
     * @return class-string<Model>
     */
    public function getModelFqn(): string
    {
        return $this->modelFqn;
    }

    /**
     * @return ?class-string<Cluster>
     */
    public function getClusterFqn(): ?string
    {
        return $this->clusterFqn;
    }

    public function getClusterBasename(): string
    {
        return class_basename($this->getClusterFqn());
    }

    public function hasCluster(): bool
    {
        if ($this->hasParentResource()) {
            return false;
        }

        return filled($this->getClusterFqn());
    }

    /**
     * @return ?class-string
     */
    public function getParentResourceFqn(): ?string
    {
        return $this->parentResourceFqn;
    }

    public function hasParentResource(): bool
    {
        return filled($this->getParentResourceFqn());
    }

    /**
     * @return array<string, array{
     *     class: class-string<Page>,
     *     path: string,
     * }>
     */
    public function getPageRoutes(): array
    {
        return $this->pageRoutes;
    }

    /**
     * @return array<string>
     */
    public function getPagesImports(): array
    {
        if ($this->hasPartialImports()) {
            return [
                (string) str(Arr::first($this->getPageRoutes())['class'])->beforeLast('\\'),
            ];
        }

        return Arr::pluck($this->getPageRoutes(), 'class');
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

    public function hasViewOperation(): bool
    {
        return $this->hasViewOperation;
    }

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    public function isSoftDeletable(): bool
    {
        return $this->isSoftDeletable;
    }

    public function isSimple(): bool
    {
        return $this->isSimple;
    }

    public function getRecordTitleAttribute(): ?string
    {
        return $this->recordTitleAttribute;
    }
}
