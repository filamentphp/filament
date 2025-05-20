<?php

namespace Filament\Support\Concerns;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;

trait Macroable
{
    /**
     * @var array<string, array<class-string, Closure>>
     */
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void
    {
        static::$macros[$name][static::class] = $macro;
    }

    public static function mixin(object $mixin, bool $replace = true): void
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED,
        );

        foreach ($methods as $method) {
            if ($replace || (! isset(static::$macros[$method->name][static::class]))) {
                $method->setAccessible(true);

                static::macro($method->name, $method->invoke($mixin));
            }
        }
    }

    public static function flushMacros(): void
    {
        static::$macros = [];
    }

    public static function hasMacro(string $name): bool
    {
        return (bool) static::getMacro($name);
    }

    /**
     * @param  array<array-key>  $parameters
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $macro = static::getMacro($method);

        if ($macro === null) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method,
            ));
        }

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo(null, static::class);
        }

        return $macro(...$parameters);
    }

    /**
     * @param  array<array-key>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        $macro = static::getMacro($method);

        if ($macro === null) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method,
            ));
        }

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return $macro(...$parameters);
    }

    protected static function getMacro(string $method): ?callable
    {
        $macros = static::$macros[$method] ?? [];

        if (! count($macros)) {
            return null;
        }

        if (array_key_exists(static::class, $macros)) {
            return $macros[static::class];
        }

        foreach (class_parents(static::class) as $parent) {
            if (! array_key_exists($parent, $macros)) {
                continue;
            }

            return $macros[$parent];
        }

        return null;
    }
}
