<?php

namespace Filament\Support\Components;

use Closure;
use Filament\Support\Components\Contracts\ScopedComponentManager;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class ComponentManager implements ScopedComponentManager
{
    /**
     * @var array<class-string, array<Closure>>
     */
    protected array $configurations = [];

    /**
     * @var array<class-string, array<Closure>>
     */
    protected array $importantConfigurations = [];

    /**
     * @var array<string, array<string>>
     */
    protected array $methodCache = [];

    final public function __construct() {}

    public static function resolve(): ScopedComponentManager
    {
        if (app()->resolved(ScopedComponentManager::class)) {
            return static::resolveScoped();
        }

        app()->singletonIf(
            ComponentManager::class,
            fn () => new ComponentManager,
        );

        return app(ComponentManager::class);
    }

    public static function resolveScoped(): ScopedComponentManager
    {
        return app(ScopedComponentManager::class);
    }

    public function clone(): static
    {
        return clone $this;
    }

    public function configureUsing(string $component, Closure $modifyUsing, ?Closure $during = null, bool $isImportant = false): mixed
    {
        if ($isImportant) {
            $this->importantConfigurations[$component] ??= [];
            $this->importantConfigurations[$component][] = $modifyUsing;
        } else {
            $this->configurations[$component] ??= [];
            $this->configurations[$component][] = $modifyUsing;
        }

        if (! $during) {
            $configurationKey = $isImportant ?
                array_key_last($this->importantConfigurations[$component]) :
                array_key_last($this->configurations[$component]);

            return function () use ($component, $configurationKey, $isImportant): void {
                if ($isImportant) {
                    unset($this->importantConfigurations[$component][$configurationKey]);

                    return;
                }

                unset($this->configurations[$component][$configurationKey]);
            };
        }

        try {
            return $during();
        } finally {
            if ($isImportant) {
                array_pop($this->importantConfigurations[$component]);
            } else {
                array_pop($this->configurations[$component]);
            }
        }
    }

    public function configure(Component $component, Closure $setUp): void
    {
        $classesToConfigure = [...array_reverse(class_parents($component)), $component::class];
        $setUpMethodClass = (new ReflectionMethod($component, 'setUp'))->getDeclaringClass()->getName();

        foreach ($classesToConfigure as $classToConfigure) {
            if ($classToConfigure === $setUpMethodClass) {
                $setUp();
            }

            if (array_key_exists($classToConfigure, $this->configurations)) {
                foreach ($this->configurations[$classToConfigure] as $configure) {
                    $configure($component);
                }
            }
        }

        foreach ($classesToConfigure as $classToConfigure) {
            if (array_key_exists($classToConfigure, $this->importantConfigurations)) {
                foreach ($this->importantConfigurations[$classToConfigure] as $configure) {
                    $configure($component);
                }
            }
        }
    }

    /**
     * @return array<string, Closure>
     */
    public function extractPublicMethods(Component $component): array
    {
        if (! isset($this->methodCache[$component::class])) {
            $reflection = new ReflectionClass($component);

            $this->methodCache[$component::class] = [];

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $name = $method->getName();

                // Skip magic methods and fluent setter methods (return `static`)
                // which are never called from Blade views. This roughly halves
                // the number of closures created per component instance.
                if (str_starts_with($name, '__')) {
                    continue;
                }

                $returnType = $method->getReturnType();

                if ($returnType instanceof ReflectionNamedType && $returnType->getName() === 'static') {
                    continue;
                }

                $this->methodCache[$component::class][] = $name;
            }
        }

        $values = [];

        foreach ($this->methodCache[$component::class] as $method) {
            $values[$method] = $component->$method(...);
        }

        return $values;
    }
}
