<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

trait EvaluatesClosures
{
    protected string $evaluationIdentifier;

    /**
     * @template T
     *
     * @param  T | callable(): T  $value
     * @param  array<string, mixed>  $namedInjections
     * @param  array<string, mixed>  $typedInjections
     * @return T
     */
    public function evaluate(mixed $value, array $namedInjections = [], array $typedInjections = []): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        $dependencies = [];

        foreach ((new ReflectionFunction($value))->getParameters() as $parameter) {
            $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter, $namedInjections, $typedInjections);
        }

        return $value(...$dependencies);
    }

    /**
     * @param  array<string, mixed>  $namedInjections
     * @param  array<string, mixed>  $typedInjections
     */
    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter, array $namedInjections, array $typedInjections): mixed
    {
        $parameterName = $parameter->getName();

        if (array_key_exists($parameterName, $namedInjections)) {
            return value($namedInjections[$parameterName]);
        }

        $typedParameterClassName = $this->getTypedReflectionParameterClassName($parameter);

        if (filled($typedParameterClassName) && array_key_exists($typedParameterClassName, $typedInjections)) {
            return value($typedInjections[$typedParameterClassName]);
        }

        // Dependencies are wrapped in an array to differentiate between null and no value.
        $defaultWrappedDependencyByName = $this->resolveDefaultClosureDependencyForEvaluationByName($parameterName);

        if (count($defaultWrappedDependencyByName)) {
            // Unwrap the dependency if it was resolved.
            return $defaultWrappedDependencyByName[0];
        }

        if (filled($typedParameterClassName)) {
            // Dependencies are wrapped in an array to differentiate between null and no value.
            $defaultWrappedDependencyByType = $this->resolveDefaultClosureDependencyForEvaluationByType($typedParameterClassName);

            if (count($defaultWrappedDependencyByType)) {
                // Unwrap the dependency if it was resolved.
                return $defaultWrappedDependencyByType[0];
            }
        }

        if (
            isset($this->evaluationIdentifier) &&
            ($parameterName === $this->evaluationIdentifier)
        ) {
            return $this;
        }

        if (filled($typedParameterClassName)) {
            return app()->make($typedParameterClassName);
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->isOptional()) {
            return null;
        }

        $staticClass = static::class;

        throw new BindingResolutionException("An attempt was made to evaluate a closure for [{$staticClass}], but [\${$parameterName}] was unresolvable.");
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return [];
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return [];
    }

    protected function getTypedReflectionParameterClassName(ReflectionParameter $parameter): ?string
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
