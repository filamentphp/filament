<?php

namespace Filament\Commands\FileGenerators\Resources\Pages;

use Filament\Commands\FileGenerators\Resources\Pages\Concerns\CanGenerateResourceProperty;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Nette\PhpGenerator\ClassType;

class ResourceCreateRecordPageClassGenerator extends ClassGenerator
{
    use CanGenerateResourceProperty;

    /**
     * @param  class-string  $resourceFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $resourceFqn,
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
        $extends = $this->getExtends();
        $extendsBasename = class_basename($extends);

        return [
            $this->getResourceFqn(),
            ...(($extendsBasename === class_basename($this->getFqn())) ? [$extends => "Base{$extendsBasename}"] : [$extends]),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return CreateRecord::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addResourcePropertyToClass($class);
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
}
