<?php

namespace Filament\Commands\FileGenerators;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Icons\Heroicon;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Property;

class ClusterClassGenerator extends ClassGenerator
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
            $this->getExtends(),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Cluster::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addNavigationIconPropertyToClass($class);
    }

    protected function addNavigationIconPropertyToClass(ClassType $class): void
    {
        $this->namespace->addUse(BackedEnum::class);
        $this->namespace->addUse(Heroicon::class);

        $property = $class->addProperty('navigationIcon', new Literal('Heroicon::OutlinedSquares2x2'))
            ->setProtected()
            ->setStatic()
            ->setType('string|BackedEnum|null');
        $this->configureNavigationIconProperty($property);
    }

    protected function configureNavigationIconProperty(Property $property): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }
}
