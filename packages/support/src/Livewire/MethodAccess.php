<?php

namespace Filament\Support\Livewire;

use Filament\Support\Attributes\Exposed;
use Filament\Support\Attributes\ServerOnly;
use Livewire\Component;
use Livewire\Exceptions\MethodNotFoundException;
use Livewire\Features\SupportEvents\SupportEvents;
use ReflectionAttribute;
use ReflectionClass;

class MethodAccess
{
    /**
     * @param  array<mixed>  $parameters
     */
    public function __invoke(Component $component, string $method, array $parameters): void
    {
        if ($this->isServerOnly($component, $method)) {
            throw new MethodNotFoundException($method);
        }

        // Event listeners bypass normal method dispatch.
        if ($method !== '__dispatch') {
            return;
        }

        if (! in_array($parameters[0], SupportEvents::getListenerEventNames($component))) {
            return;
        }

        $listenerMethod = SupportEvents::getListenerMethodName($component, $parameters[0]);

        if ($this->isServerOnly($component, $listenerMethod)) {
            throw new MethodNotFoundException($listenerMethod);
        }
    }

    protected function isServerOnly(Component $component, string $method): bool
    {
        $class = new ReflectionClass($component);

        while ($class && $class->hasMethod($method)) {
            $reflectionMethod = $class->getMethod($method);

            if ($reflectionMethod->getAttributes(ServerOnly::class, ReflectionAttribute::IS_INSTANCEOF)) {
                return true;
            }

            if ($reflectionMethod->getAttributes(Exposed::class, ReflectionAttribute::IS_INSTANCEOF)) {
                return false;
            }

            $class = $reflectionMethod->getDeclaringClass()->getParentClass();
        }

        return false;
    }
}
