<?php

namespace Filament\Support\Components;

use Closure;
use Filament\Support\Components\Contracts\ScopedComponentManager;
use ReflectionClass;
use ReflectionMethod;

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

    final public function __construct()
    {
    }

    public static function resolve(): ScopedComponentManager
    {
        if (app()->resolved(ScopedComponentManager::class)) {
            return static::resolveScoped();
        }

        app()->singletonIf(
            ComponentManager::class,
            fn () => new ComponentManager(),
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
            return null;
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
        foreach ($this->configurations as $classToConfigure => $configurationCallbacks) {
            if (! $component instanceof $classToConfigure) {
                continue;
            }

            foreach ($configurationCallbacks as $configure) {
                $configure($component);
            }
        }

        $setUp();

        foreach ($this->importantConfigurations as $classToConfigure => $configurationCallbacks) {
            if (! $component instanceof $classToConfigure) {
                continue;
            }

            foreach ($configurationCallbacks as $configure) {
                $configure($component);
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

            $this->methodCache[$component::class] = array_map(
                fn (ReflectionMethod $method): string => $method->getName(),
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            );
        }

        $values = [];

        foreach ($this->methodCache[$component::class] as $method) {
            $values[$method] = $component->$method(...);
        }

        return $values;
    }
}
