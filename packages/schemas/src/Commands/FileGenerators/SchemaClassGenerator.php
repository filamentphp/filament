<?php

namespace Filament\Schemas\Commands\FileGenerators;

use Filament\Schemas\Schema;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class SchemaClassGenerator extends ClassGenerator
{
    final public function __construct(
        protected string $fqn,
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
            Schema::class,
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addConfigureMethodToClass($class);
    }

    protected function addConfigureMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('configure')
            ->setPublic()
            ->setStatic()
            ->setReturnType(Schema::class)
            ->setBody(<<<'PHP'
                return $schema
                    ->components([
                        //
                    ]);
                PHP);
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureConfigureMethod($method);
    }

    protected function configureConfigureMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }
}
