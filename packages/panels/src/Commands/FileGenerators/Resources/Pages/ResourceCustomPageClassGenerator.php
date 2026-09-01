<?php

namespace Filament\Commands\FileGenerators\Resources\Pages;

use Filament\Commands\FileGenerators\Resources\Pages\Concerns\CanGenerateResourceProperty;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Commands\FileGenerators\Concerns\CanGenerateViewProperty;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitUse;

class ResourceCustomPageClassGenerator extends ClassGenerator
{
    use CanGenerateResourceProperty;
    use CanGenerateViewProperty;

    /**
     * @param  class-string  $resourceFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $resourceFqn,
        protected string $view,
        protected bool $hasRecord,
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
            ...($this->hasRecord ? [InteractsWithRecord::class] : []),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Page::class;
    }

    protected function addTraitsToClass(ClassType $class): void
    {
        $this->addInteractsWithRecordTraitToClass($class);
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addResourcePropertyToClass($class);
        $this->addViewPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addMountMethodToClass($class);
    }

    protected function addInteractsWithRecordTraitToClass(ClassType $class): void
    {
        if (! $this->hasRecord) {
            return;
        }

        $trait = $class->addTrait(InteractsWithRecord::class);
        $this->configureInteractsWithRecordTrait($trait);
    }

    protected function configureInteractsWithRecordTrait(TraitUse $trait): void {}

    protected function addMountMethodToClass(ClassType $class): void
    {
        if (! $this->hasRecord) {
            return;
        }

        $method = $class->addMethod('mount')
            ->setPublic()
            ->setReturnType('void')
            ->setBody(<<<'PHP'
                $this->record = $this->resolveRecord($record);
                PHP);
        $method->addParameter('record')
            ->setType('int|string');

        $this->configureMountMethod($method);
    }

    protected function configureMountMethod(Method $method): void {}

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

    public function getView(): string
    {
        return $this->view;
    }
}
