<?php

namespace Filament\Commands\FileGenerators\Resources\Schemas;

use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class ResourceTableClassGenerator extends ClassGenerator
{
    use CanGenerateResourceTables;
    use CanReadModelSchemas;

    /**
     * @param  class-string<Model>  $modelFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $modelFqn,
        protected ?string $parentResourceFqn,
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
            Table::class,
            ...($this->hasPartialImports() ? ['Filament\Actions', 'Filament\Tables'] : []),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addConfigureMethodToClass($class);
    }

    protected function addConfigureMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('configure')
            ->setPublic()
            ->setStatic()
            ->setReturnType(Table::class)
            ->setBody($this->generateTableMethodBody($this->getModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate())));
        $method->addParameter('table')
            ->setType(Table::class);

        $this->configureConfigureMethod($method);
    }

    protected function configureConfigureMethod(Method $method): void {}

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

    public function getModelBasename(): string
    {
        return class_basename($this->getModelFqn());
    }

    /**
     * @return ?class-string
     */
    public function getParentResourceFqn(): ?string
    {
        return $this->parentResourceFqn;
    }

    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * @return class-string<Model>
     */
    public function getModelFqn(): string
    {
        return $this->modelFqn;
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
}
