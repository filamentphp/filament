<?php

namespace Filament\Forms\Commands\FileGenerators;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Illuminate\Support\Stringable;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class RichContentCustomBlockClassGenerator extends ClassGenerator
{
    final public function __construct(
        protected string $fqn,
        protected string $view,
        protected string $previewView,
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
            Action::class,
            $this->getExtends(),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return RichContentCustomBlock::class;
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addGetIdMethodToClass($class);
        $this->addGetLabelMethodToClass($class);
        $this->addConfigureEditorActionMethodToClass($class);
        $this->addToPreviewHtmlMethodToClass($class);
        $this->addToHtmlMethodToClass($class);
    }

    protected function addGetIdMethodToClass(ClassType $class): void
    {
        $id = (string) str($this->getBasename())
            ->whenEndsWith('Block', fn (Stringable $stringable) => $stringable->beforeLast('Block'))
            ->snake()
            ->lower();

        $method = $class->addMethod('getId')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody(
                <<<PHP
                return '{$id}';
                PHP,
            );

        $this->configureGetIdMethod($method);
    }

    protected function configureGetIdMethod(Method $method): void {}

    protected function addGetLabelMethodToClass(ClassType $class): void
    {
        $label = (string) str($this->getBasename())
            ->whenEndsWith('Block', fn (Stringable $stringable) => $stringable->beforeLast('Block'))
            ->kebab()
            ->replace('-', ' ')
            ->ucfirst();

        $method = $class->addMethod('getLabel')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody(
                <<<PHP
                return '{$label}';
                PHP,
            );

        $this->configureGetLabelMethod($method);
    }

    protected function configureGetLabelMethod(Method $method): void {}

    protected function addConfigureEditorActionMethodToClass(ClassType $class): void
    {
        $label = (string) str($this->getBasename())
            ->kebab()
            ->replace('-', ' ');

        $method = $class->addMethod('configureEditorAction')
            ->setPublic()
            ->setStatic()
            ->setReturnType(Action::class)
            ->setBody(
                <<<PHP
                return \$action
                    ->modalDescription('Configure the {$label}')
                    ->schema([
                        //
                    ]);
                PHP,
            );
        $method->addParameter('action')
            ->setType(Action::class);

        $this->configureConfigureEditorActionMethod($method);
    }

    protected function configureConfigureEditorActionMethod(Method $method): void {}

    protected function addToPreviewHtmlMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('toPreviewHtml')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody(
                <<<PHP
                return view('{$this->previewView}', [
                    //
                ])->render();
                PHP,
            );
        $method->addParameter('config')
            ->setType('array');

        $this->configureToPreviewMethod($method);
    }

    protected function configureToPreviewMethod(Method $method): void {}

    protected function addToHtmlMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('toHtml')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody(
                <<<PHP
                return view('{$this->view}', [
                    //
                ])->render();
                PHP,
            );
        $method->addParameter('config')
            ->setType('array');
        $method->addParameter('data')
            ->setType('array');

        $this->configureToHtmlMethod($method);
    }

    protected function configureToHtmlMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getView(): ?string
    {
        return $this->view;
    }
}
