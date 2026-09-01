<?php

namespace Filament\Schemas\Commands\FileGenerators;

use Filament\Schemas\Components\Component;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

class ComponentClassGenerator extends ClassGenerator
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
        return Component::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addViewPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addMakeMethodToClass($class);
    }

    protected function addViewPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('view', $this->getView())
            ->setProtected()
            ->setType('string');
        $this->configureViewProperty($property);
    }

    protected function configureViewProperty(Property $property): void {}

    protected function addMakeMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('make')
            ->setPublic()
            ->setStatic()
            ->setReturnType('static')
            ->setBody(<<<'PHP'
                return app(static::class);
                PHP);

        $this->configureMakeMethod($method);
    }

    protected function configureMakeMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
