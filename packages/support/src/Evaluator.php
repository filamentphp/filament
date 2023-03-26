<?php

namespace Filament\Support;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Container\Util;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

class Evaluator
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __invoke(Closure $callback, array $parameters = []): mixed
    {
        return $callback(...static::getClosureDependencies($callback, $parameters));
    }

    /**
     * @param array<string, mixed> $parameters
     * @return array<mixed>
     */
    protected static function getClosureDependencies(Closure $callback, array $parameters = []): array
    {
        $dependencies = [];

        $container = app();

        foreach ((new ReflectionFunction($callback))->getParameters() as $parameter) {
            static::injectDependencyForParameter($container, $parameter, $parameters, $dependencies);
        }

        return $dependencies;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<mixed> $dependencies
     */
    protected static function injectDependencyForParameter(Container $container, ReflectionParameter $parameter, array &$parameters, array &$dependencies): void
    {
        $parameterName = $parameter->getName();

        if (array_key_exists($parameterName, $parameters)) {
            $dependencies[] = value($parameters[$parameterName]);

            unset($parameters[$parameterName]);

            return;
        }

        $typedParameterClassName = static::getTypedParameterClassName($parameter);

        if (filled($typedParameterClassName)) {
            $dependencies[] = $container->make($typedParameterClassName);

            return;
        }

        if ($parameter->isDefaultValueAvailable()) {
            $dependencies[] = $parameter->getDefaultValue();

            return;
        }

        if ($parameter->isOptional()) {
            return;
        }

        throw new BindingResolutionException("An attempt was made to evaluate a closure, but [${$parameter}] was unresolvable.");
    }

    protected static function getTypedParameterClassName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType) {
            return null;
        }

        if ($type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        $class = $parameter->getDeclaringClass();

        if (blank($class)) {
            return $name;
        }

        if ($name === 'self') {
            return $class->getName();
        }

        if ($name === 'parent' && ($parent = $class->getParentClass())) {
            return $parent->getName();
        }

        return $name;
    }
}
