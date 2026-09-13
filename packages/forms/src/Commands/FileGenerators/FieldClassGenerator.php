<?php

namespace Filament\Forms\Commands\FileGenerators;

use Filament\Forms\Components\Concerns\HasJsRenderer;
use Filament\Forms\Components\Field;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Illuminate\Support\Facades\Vite;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;

class FieldClassGenerator extends ClassGenerator
{
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
        return [
            $this->getExtends(),
            ...($this->renderer ? [HasJsRenderer::class, HasEmbeddedView::class, Vite::class] : []),
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
        if ($this->renderer) {
            return;
        }

        $this->addViewPropertyToClass($class);
    }

    public function getImplements(): array
    {
        return $this->renderer ? [HasEmbeddedView::class] : [];
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
