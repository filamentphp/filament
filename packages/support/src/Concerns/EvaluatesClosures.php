<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Evaluator;
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
     * @param  array<string, mixed>  $parameters
     * @return T
     */
    public function evaluate(mixed $value, array $parameters = []): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        $dependencies = [];

        foreach ((new ReflectionFunction($value))->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            if (array_key_exists($parameterName, $parameters)) {
                $dependencies[] = value($parameters[$parameterName]);

                continue;
            }

            $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter);
        }

        return $value(...$dependencies);
    }

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        $parameterName = $parameter->getName();

        if (
            isset($this->evaluationIdentifier) &&
            ($parameterName === $this->evaluationIdentifier)
        ) {
            return $this;
        }

        $typedParameterClassName = $this->getTypedReflectionParameterClassName($parameter);

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
        throw new BindingResolutionException("An attempt was made to evaluate a closure for [{$staticClass}], but [${$parameterName}] was unresolvable.");
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
