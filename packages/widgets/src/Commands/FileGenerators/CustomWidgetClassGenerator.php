<?php

namespace Filament\Widgets\Commands\FileGenerators;

use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Commands\FileGenerators\Concerns\CanGenerateViewProperty;
use Filament\Widgets\Concerns\HasJsRenderer;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Vite;
use Nette\PhpGenerator\ClassType;

class CustomWidgetClassGenerator extends ClassGenerator
{
    use CanGenerateViewProperty;

    final public function __construct(
        protected string $fqn,
        protected string $view,
        protected ?string $renderer = null,
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
            ...($this->renderer ? [HasJsRenderer::class, Vite::class] : []),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Widget::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        if ($this->renderer) {
            return;
        }

        $this->addViewPropertyToClass($class);
    }

    protected function addTraitsToClass(ClassType $class): void
    {
        if ($this->renderer) {
            $class->addTrait(HasJsRenderer::class);
        }
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        if (! $this->renderer) {
            return;
        }

        $class->addMethod('getRenderer')
            ->setPublic()
            ->setReturnType('string')
            ->setBody('return ' . $this->simplifyFqn(Vite::class) . '::asset(?);', [$this->renderer]);
    }

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
