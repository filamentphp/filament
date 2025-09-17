<?php

namespace Filament\Widgets\Commands\FileGenerators;

use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Widgets\ChartWidget;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

class ChartWidgetClassGenerator extends ClassGenerator
{
    final public function __construct(
        protected string $fqn,
        protected string $type,
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
            ...(($extendsBasename === class_basename($this->getFqn())) ? [$extends => "Base{$extendsBasename}"] : [$extends]),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return ChartWidget::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addHeadingPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addGetDataMethodToClass($class);
        $this->addGetTypeMethodToClass($class);
    }

    protected function addHeadingPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty(
            'heading',
            (string) str($this->getBasename())
                ->classBasename()
                ->kebab()
                ->replace('-', ' ')
                ->ucwords(),
        )
            ->setProtected()
            ->setType('?string');
        $this->configureHeadingProperty($property);
    }

    protected function configureHeadingProperty(Property $property): void {}

    protected function addGetDataMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('getData')
            ->setProtected()
            ->setReturnType('array')
            ->setBody(<<<'PHP'
                return [
                    //
                ];
                PHP);

        $this->configureGetDataMethod($method);
    }

    protected function configureGetDataMethod(Method $method): void {}

    protected function addGetTypeMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('getType')
            ->setProtected()
            ->setReturnType('string')
            ->setBody(new Literal(<<<'PHP'
                return ?;
                PHP, [$this->getType()]));

        $this->configureGetTypeMethod($method);
    }

    protected function configureGetTypeMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
