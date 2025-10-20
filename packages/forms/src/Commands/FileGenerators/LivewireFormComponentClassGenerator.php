<?php

namespace Filament\Forms\Commands\FileGenerators;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Commands\FileGenerators\Concerns\CanGenerateModelForms;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\TraitUse;

class LivewireFormComponentClassGenerator extends ClassGenerator
{
    use CanGenerateModelForms;
    use CanReadModelSchemas;

    /**
     * @param  ?class-string<Model>  $modelFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $submitAction,
        protected ?string $modelFqn,
        protected ?bool $isGenerated,
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
            ...($this->hasPartialImports() ? [
                'Filament\Forms',
                'Filament\Schemas',
            ] : []),
            ...(($modelFqn = $this->getModelFqn()) ? [$modelFqn] : []),
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

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addRecordPropertyToClass($class);
        $this->addDataPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addMountMethodToClass($class);
        $this->addFormMethodToClass($class);
        $this->addSubmitMethodToClass($class);
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

    protected function addRecordPropertyToClass(ClassType $class): void
    {
        if ($this->getSubmitAction() !== 'save') {
            return;
        }

        $property = $class->addProperty('record')
            ->setPublic()
            ->setType($this->getModelFqn());
        $this->configureRecordProperty($property);
    }

    protected function configureRecordProperty(Property $property): void {}

    protected function addDataPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('data', [])
            ->setPublic()
            ->setType('?array');
        $this->configureDataProperty($property);
    }

    protected function configureDataProperty(Property $property): void {}

    protected function addMountMethodToClass(ClassType $class): void
    {
        $submitAction = $this->getSubmitAction();

        $method = $class->addMethod('mount')
            ->setPublic()
            ->setReturnType('void')
            ->setBody(match ($submitAction) {
                'save' => <<<'PHP'
                    $this->form->fill($this->record->attributesToArray());
                    PHP,
                default => <<<'PHP'
                    $this->form->fill();
                    PHP,
            });

        $this->configureMountMethod($method);
    }

    protected function configureMountMethod(Method $method): void {}

    protected function addFormMethodToClass(ClassType $class): void
    {
        $submitAction = $this->getSubmitAction();
        $modelFqn = $this->getModelFqn();

        $method = $class->addMethod('form')
            ->setPublic()
            ->setReturnType(Schema::class)
            ->setBody(
                filled($modelFqn)
                ? $this->generateFormMethodBody($this->getModelFqn(), statePath: 'data', modelMethodOutput: match ($submitAction) {
                    'create' => "->model({$this->simplifyFqn($modelFqn)}::class)",
                    'save' => '->model($this->record)',
                    default => null,
                })
                : <<<'PHP'
                    return $schema
                        ->components([
                            //
                        ])
                        ->statePath('data');
                    PHP,
            );
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureFormMethod($method);
    }

    protected function configureFormMethod(Method $method): void {}

    protected function addSubmitMethodToClass(ClassType $class): void
    {
        $submitAction = $this->getSubmitAction();
        $modelFqn = $this->getModelFqn();

        $method = $class->addMethod($submitAction)
            ->setPublic()
            ->setReturnType('void')
            ->setBody(match ($submitAction) {
                'create' => <<<PHP
                    \$data = \$this->form->getState();

                    \$record = {$this->simplifyFqn($modelFqn)}::create(\$data);

                    \$this->form->model(\$record)->saveRelationships();
                    PHP,
                'save' => <<<'PHP'
                    $data = $this->form->getState();

                    $this->record->update($data);
                    PHP,
                default => <<<'PHP'
                    $data = $this->form->getState();

                    //
                    PHP,
            });

        $this->configureSubmitMethod($method);
    }

    protected function configureSubmitMethod(Method $method): void {}

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

    public function getSubmitAction(): string
    {
        return $this->submitAction;
    }

    /**
     * @return ?class-string<Model>
     */
    public function getModelFqn(): ?string
    {
        return $this->modelFqn;
    }

    public function isGenerated(): ?bool
    {
        return $this->isGenerated;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
