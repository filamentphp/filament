<?php

namespace Filament\Tables\Commands\FileGenerators;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Tables\Commands\FileGenerators\Concerns\CanGenerateModelTables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitUse;

class LivewireTableComponentClassGenerator extends ClassGenerator
{
    use CanGenerateModelTables;
    use CanReadModelSchemas;

    /**
     * @param  class-string<Model>  $modelFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $modelFqn,
        protected bool $isGenerated,
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
            InteractsWithTable::class,
            InteractsWithSchemas::class,
            Table::class,
            ...($this->hasPartialImports() ? ['Filament\Tables'] : []),
            Builder::class,
            $this->getModelFqn(),
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
            HasTable::class,
        ];
    }

    protected function addTraitsToClass(ClassType $class): void
    {
        $this->addInteractsWithActionsTraitToClass($class);
        $this->addInteractsWithTableTraitToClass($class);
        $this->addInteractsWithSchemasTraitToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addTableMethodToClass($class);
        $this->addRenderMethodToClass($class);
    }

    protected function addInteractsWithActionsTraitToClass(ClassType $class): void
    {
        $trait = $class->addTrait(InteractsWithActions::class);
        $this->configureInteractsWithActionsTrait($trait);
    }

    protected function configureInteractsWithActionsTrait(TraitUse $trait): void {}

    protected function addInteractsWithTableTraitToClass(ClassType $class): void
    {
        $trait = $class->addTrait(InteractsWithTable::class);
        $this->configureInteractsWithTableTrait($trait);
    }

    protected function configureInteractsWithTableTrait(TraitUse $trait): void {}

    protected function addInteractsWithSchemasTraitToClass(ClassType $class): void
    {
        $trait = $class->addTrait(InteractsWithSchemas::class);
        $this->configureInteractsWithSchemasTrait($trait);
    }

    protected function configureInteractsWithSchemasTrait(TraitUse $trait): void {}

    protected function addTableMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('table')
            ->setPublic()
            ->setReturnType(Table::class)
            ->setBody($this->generateTableMethodBody($this->getModelFqn()));
        $method->addParameter('table')
            ->setType(Table::class);

        $this->configureTableMethod($method);
    }

    protected function configureTableMethod(Method $method): void {}

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

    public function getView(): string
    {
        return $this->view;
    }
}
