<?php

namespace Filament\Support\Livewire;

use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Livewire\Component;
use Livewire\Features\SupportEvents\SupportEvents;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

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
        return $this->getMethodAccess(new ReflectionClass($component), $method) === true;
    }

    /**
     * @template T of object
     *
     * @param  ReflectionClass<T>  $class
     */
    public function getMethodAccess(ReflectionClass $class, string $method): ?bool
    {
        while ($class && $class->hasMethod($method)) {
            $reflectionMethod = $class->getMethod($method);
            $class = $reflectionMethod->getDeclaringClass();

            $access = $this->getDeclaredMethodAccess($reflectionMethod)
                ?? $this->getTraitMethodAccess($class, $method, $reflectionMethod);

            if ($access !== null) {
                return $access;
            }

            $class = $class->getParentClass();
        }

        return null;
    }

    protected function getDeclaredMethodAccess(ReflectionMethod $method): ?bool
    {
        if ($method->getAttributes(Unexposed::class, ReflectionAttribute::IS_INSTANCEOF)) {
            return true;
        }

        if ($method->getAttributes(Exposed::class, ReflectionAttribute::IS_INSTANCEOF)) {
            return false;
        }

        return null;
    }

    /** @param ReflectionClass<object> $class */
    protected function getTraitMethodAccess(ReflectionClass $class, string $method, ReflectionMethod $implementation): ?bool
    {
        $traitMethods = [];
        $traits = array_change_key_case($class->getTraits());

        foreach ($traits as $trait) {
            if ($trait->hasMethod($method)) {
                $traitMethods[] = $trait->getMethod($method);
            }
        }

        foreach ($class->getTraitAliases() as $alias => $original) {
            if (strcasecmp($alias, $method) !== 0) {
                continue;
            }

            [$trait, $originalMethod] = explode('::', $original, 2);
            $traitMethods[] = $traits[strtolower($trait)]->getMethod($originalMethod);
        }

        $isDeclaredInClass = ($implementation->getFileName() === $class->getFileName()) &&
            ($implementation->getStartLine() >= $class->getStartLine()) &&
            ($implementation->getEndLine() <= $class->getEndLine());

        $selectedTraitMethods = $isDeclaredInClass ? [] : array_filter($traitMethods, static fn (ReflectionMethod $traitMethod): bool => ($traitMethod->getFileName() === $implementation->getFileName()) &&
            ($traitMethod->getStartLine() === $implementation->getStartLine()) &&
            ($traitMethod->getEndLine() === $implementation->getEndLine()));

        $isExposed = false;

        foreach ($selectedTraitMethods ?: $traitMethods as $traitMethod) {
            $access = $this->getDeclaredMethodAccess($traitMethod)
                ?? $this->getTraitMethodAccess($traitMethod->getDeclaringClass(), $traitMethod->getName(), $traitMethod);

            if ($access === true) {
                return true;
            }

            $isExposed = $isExposed || ($access === false);
        }

        return $isExposed ? false : null;
    }
}
