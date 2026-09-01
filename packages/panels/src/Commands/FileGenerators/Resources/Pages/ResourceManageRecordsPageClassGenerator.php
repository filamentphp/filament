<?php

namespace Filament\Commands\FileGenerators\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Commands\FileGenerators\Concerns\CanGenerateGetHeaderActionsMethod;
use Filament\Commands\FileGenerators\Resources\Pages\Concerns\CanGenerateResourceProperty;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Nette\PhpGenerator\ClassType;

class ResourceManageRecordsPageClassGenerator extends ClassGenerator
{
    use CanGenerateGetHeaderActionsMethod;
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
            ...($this->hasPartialImports() ? [
                'Filament\Actions',
            ] : $this->getHeaderActions()),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return ManageRecords::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addResourcePropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addGetHeaderActionsMethodToClass($class);
    }

    /**
     * @return array<class-string<Action>>
     */
    public function getHeaderActions(): array
    {
        return [
            CreateAction::class,
        ];
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
