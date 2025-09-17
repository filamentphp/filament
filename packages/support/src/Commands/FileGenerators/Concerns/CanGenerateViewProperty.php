<?php

namespace Filament\Support\Commands\FileGenerators\Concerns;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;

trait CanGenerateViewProperty
{
    protected function addViewPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('view', $this->getView())
            ->setProtected()
            ->setType('string');
        $this->configureViewProperty($property);
    }

    protected function configureViewProperty(Property $property): void {}
}
