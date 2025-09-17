<?php

namespace Filament\Commands\FileGenerators\Concerns;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

trait CanGenerateGetHeaderActionsMethod
{
    protected function addGetHeaderActionsMethodToClass(ClassType $class): void
    {
        $actions = array_map(
            fn (string $action): string => (string) new Literal("{$this->simplifyFqn($action)}::make(),"),
            $this->getHeaderActions(),
        );

        $actionsOutput = implode(PHP_EOL . '    ', $actions);

        $method = $class->addMethod('getHeaderActions')
            ->setProtected()
            ->setReturnType('array')
            ->setBody(
                <<<PHP
                return [
                    {$actionsOutput}
                ];
                PHP
            );

        $this->configureGetHeaderActionsMethod($method);
    }

    protected function configureGetHeaderActionsMethod(Method $method): void {}
}
