<?php

namespace Filament\Commands\FileGenerators\Resources\Pages\Concerns;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Property;

trait CanGenerateResourceProperty
{
    protected function addResourcePropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('resource', new Literal("{$this->simplifyFqn($this->getResourceFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('string');
        $this->configureResourceProperty($property);
    }

    protected function configureResourceProperty(Property $property): void {}
}
