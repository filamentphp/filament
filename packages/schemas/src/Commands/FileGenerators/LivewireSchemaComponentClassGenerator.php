<?php

namespace Filament\Schemas\Commands\FileGenerators;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitUse;

class LivewireSchemaComponentClassGenerator extends ClassGenerator
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
            ...$this->getImplements(),
            InteractsWithActions::class,
            InteractsWithSchemas::class,
            Schema::class,
            View::class,
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

    /**
     * @return array<class-string>
     */
    public function getImplements(): array
    {
        return [
            HasActions::class,
            HasSchemas::class,
        ];
    }

    protected function addTraitsToClass(ClassType $class): void
    {
        $this->addInteractsWithActionsTraitToClass($class);
        $this->addInteractsWithSchemasTraitToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addContentMethodToClass($class);
        $this->addRenderMethodToClass($class);
    }

    protected function addInteractsWithActionsTraitToClass(ClassType $class): void
    {
        $trait = $class->addTrait(InteractsWithActions::class);
        $this->configureInteractsWithActionsTrait($trait);
    }

    protected function configureInteractsWithActionsTrait(TraitUse $trait): void {}

    protected function addInteractsWithSchemasTraitToClass(ClassType $class): void
    {
        $trait = $class->addTrait(InteractsWithSchemas::class);
        $this->configureInteractsWithSchemasTrait($trait);
    }

    protected function configureInteractsWithSchemasTrait(TraitUse $trait): void {}

    protected function addContentMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('content')
            ->setPublic()
            ->setReturnType(Schema::class)
            ->setBody(<<<'PHP'
                return $schema
                    ->components([
                        //
                    ]);
                PHP);
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureContentMethod($method);
    }

    protected function configureContentMethod(Method $method): void {}

    protected function addRenderMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('render')
            ->setPublic()
            ->setReturnType(View::class)
            ->setBody(new Literal(<<<'PHP'
                return view(?);
                PHP, [$this->getView()]));

        $this->configureRenderMethod($method);
    }

    protected function configureRenderMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
