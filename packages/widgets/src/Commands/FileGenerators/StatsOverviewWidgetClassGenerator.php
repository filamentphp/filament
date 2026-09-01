<?php

namespace Filament\Widgets\Commands\FileGenerators;

use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class StatsOverviewWidgetClassGenerator extends ClassGenerator
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
        $extends = $this->getExtends();
        $extendsBasename = class_basename($extends);

        return [
            ...(($extendsBasename === class_basename($this->getFqn())) ? [$extends => "Base{$extendsBasename}"] : [$extends]),
            Stat::class,
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return StatsOverviewWidget::class;
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addGetStatsMethodToClass($class);
    }

    protected function addGetStatsMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('getStats')
            ->setProtected()
            ->setReturnType('array')
            ->setBody(<<<'PHP'
                return [
                    //
                ];
                PHP);

        $this->configureGetStatsMethod($method);
    }

    protected function configureGetStatsMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }
}
