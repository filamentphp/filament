<?php

namespace Filament\Support\Livewire;

use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Components\Component as FilamentComponent;
use Livewire\Component;
use Livewire\Features\SupportEvents\SupportEvents;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

class MethodAccess
{
    /**
     * @param  array<mixed>  $parameters
     */
    public function __invoke(Component $component, string $method, array $parameters): void
    {
        abort_if($this->isUnexposed($component, $method), 403);

        // Event listeners bypass normal method dispatch.
        if ($method !== '__dispatch') {
            return;
        }

        if (! in_array($parameters[0], SupportEvents::getListenerEventNames($component))) {
            return;
        }

        $listenerMethod = SupportEvents::getListenerMethodName($component, $parameters[0]);

        abort_if($this->isUnexposed($component, $listenerMethod), 403);
    }

    protected function isUnexposed(Component $component, string $method): bool
    {
        $class = new ReflectionClass($component);
        $returnType = $class->hasMethod($method) ? $class->getMethod($method)->getReturnType() : null;

        while ($class && $class->hasMethod($method)) {
            $reflectionMethod = $class->getMethod($method);

            if ($reflectionMethod->getAttributes(Unexposed::class, ReflectionAttribute::IS_INSTANCEOF)) {
                return true;
            }

            if ($reflectionMethod->getAttributes(Exposed::class, ReflectionAttribute::IS_INSTANCEOF)) {
                return false;
            }

            $class = $reflectionMethod->getDeclaringClass()->getParentClass();
        }

        $returnTypes = [$returnType];

        while ($returnTypes) {
            $type = array_pop($returnTypes);

            if (($type instanceof ReflectionUnionType) || ($type instanceof ReflectionIntersectionType)) {
                array_push($returnTypes, ...$type->getTypes());

                continue;
            }

            if (($type instanceof ReflectionNamedType) && is_a($type->getName(), FilamentComponent::class, allow_string: true)) {
                return true;
            }
        }

        return false;
    }
}
