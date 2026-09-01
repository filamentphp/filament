<?php

namespace Filament\Forms\Commands\FileGenerators;

use Filament\Forms\Components\Field;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;

class FieldClassGenerator extends ClassGenerator
{
    final public function __construct(
        protected string $fqn,
        protected string $view,
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
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Field::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addViewPropertyToClass($class);
    }

    protected function addViewPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('view', $this->getView())
            ->setProtected()
            ->setType('string');
        $this->configureViewProperty($property);
    }

    protected function configureViewProperty(Property $property): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getView(): ?string
    {
        return $this->view;
    }
}
