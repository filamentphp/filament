<?php

namespace Filament\Commands\FileGenerators\Resources\Schemas;

use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceInfolists;
use Filament\Schemas\Schema;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class ResourceInfolistSchemaClassGenerator extends ClassGenerator
{
    use CanGenerateResourceInfolists;
    use CanReadModelSchemas;

    final public function __construct(
        protected string $fqn,
        protected string $modelFqn,
        protected ?string $parentResourceFqn,
        protected bool $isGenerated,
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
            Schema::class,
            ...($this->hasPartialImports() ? ['Filament\Infolists'] : []),
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
            ->setReturnType(Schema::class)
            ->setBody($this->generateInfolistMethodBody($this->getModelFqn(), exceptColumns: Arr::wrap($this->getForeignKeyColumnToNotGenerate())));
        $method->addParameter('schema')
            ->setType(Schema::class);

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

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }
}
