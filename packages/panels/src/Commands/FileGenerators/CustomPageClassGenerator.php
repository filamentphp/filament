<?php

namespace Filament\Commands\FileGenerators;

use Filament\Clusters\Cluster;
use Filament\Pages\Page;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Commands\FileGenerators\Concerns\CanGenerateViewProperty;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Property;

class CustomPageClassGenerator extends ClassGenerator
{
    use CanGenerateViewProperty;

    /**
     * @param  ?class-string<Cluster>  $clusterFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $view,
        protected ?string $clusterFqn,
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
            ...($this->hasCluster() ? (($this->getClusterBasename() === 'Page') ? [$this->getClusterFqn() => 'PageCluster'] : [$this->getClusterFqn()]) : []),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Page::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addViewPropertyToClass($class);
        $this->addClusterPropertyToClass($class);
    }

    protected function addClusterPropertyToClass(ClassType $class): void
    {
        if (! $this->hasCluster()) {
            return;
        }

        $property = $class->addProperty('cluster', new Literal("{$this->simplifyFqn($this->getClusterFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureClusterProperty($property);
    }

    protected function configureClusterProperty(Property $property): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @return ?class-string<Cluster>
     */
    public function getClusterFqn(): ?string
    {
        return $this->clusterFqn;
    }

    public function getClusterBasename(): string
    {
        return class_basename($this->getClusterFqn());
    }

    public function hasCluster(): bool
    {
        return filled($this->getClusterFqn());
    }
}
