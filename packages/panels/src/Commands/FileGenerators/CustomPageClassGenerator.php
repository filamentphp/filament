<?php

namespace Filament\Commands\FileGenerators;

use Filament\Clusters\Cluster;
use Filament\Pages\Concerns\InteractsWithInertia;
use Filament\Pages\Page;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Commands\FileGenerators\Concerns\CanGenerateViewProperty;
use Inertia\Inertia;
use Inertia\Response;
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
        protected ?string $inertiaComponent = null,
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

        $imports = [
            ...(($extendsBasename === class_basename($this->getFqn())) ? [$extends => "Base{$extendsBasename}"] : [$extends]),
            ...($this->hasCluster() ? (($this->getClusterBasename() === 'Page') ? [$this->getClusterFqn() => 'PageCluster'] : [$this->getClusterFqn()]) : []),
        ];

        if (filled($this->inertiaComponent)) {
            foreach ([InteractsWithInertia::class, Inertia::class, Response::class] as $import) {
                $basename = class_basename($import);
                $imports[$import] = ($basename === $this->getBasename()) ? "Base{$basename}" : $basename;
            }
        }

        return $imports;
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Page::class;
    }

    protected function addTraitsToClass(ClassType $class): void
    {
        if (filled($this->inertiaComponent)) {
            $class->addTrait(InteractsWithInertia::class);
        }
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        if (blank($this->inertiaComponent)) {
            $this->addViewPropertyToClass($class);
        }

        $this->addClusterPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        if (blank($this->inertiaComponent)) {
            return;
        }

        $class->addMethod('getInertiaResponse')
            ->setProtected()
            ->setReturnType(Response::class)
            ->setBody('return ' . $this->simplifyFqn(Inertia::class) . "::render(?, [\n    'title' => ?,\n]);", [
                $this->inertiaComponent,
                (string) str($this->getBasename())->headline(),
            ]);
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
