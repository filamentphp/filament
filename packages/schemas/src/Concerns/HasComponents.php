<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

trait HasComponents
{
    /**
     * @var array<Component | Action | ActionGroup | string | Htmlable> | Component | Action | ActionGroup | string | Htmlable | Closure
     */
    protected array | Component | Action | ActionGroup | string | Htmlable | Closure $components = [];

    /**
     * @var array<array<array<array<array<string, Component| Action | ActionGroup>>>>>
     */
    protected array $cachedFlatComponents = [];

    /**
     * @var array<Component | Action | ActionGroup> | null
     */
    protected ?array $cachedComponents = null;

    /**
     * @var array<array<array<string, Component>>>
     */
    protected array $cachedComponentsByStatePath = [];

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Component | Action | ActionGroup | string | Htmlable | Closure  $components
     */
    public function components(array | Component | Action | ActionGroup | string | Htmlable | Closure $components): static
    {
        $this->components = $components;
        $this->cachedComponents = null;
        $this->cachedFlatComponents = [];
        $this->cachedComponentsByStatePath = [];

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Component | Action | ActionGroup | string | Htmlable | Closure  $components
     */
    public function schema(array | Component | Action | ActionGroup | string | Htmlable | Closure $components): static
    {
        $this->components($components);

        return $this;
    }

    public function getAction(string $actionName, ?string $nestedContainerKey = null): ?Action
    {
        foreach ($this->getComponents() as $component) {
            if (blank($nestedContainerKey)) {
                if (
                    ($component instanceof Action) &&
                    ($component->getName() === $actionName)
                ) {
                    return $component;
                }

                if (
                    ($component instanceof ActionGroup) &&
                    ($action = ($component->getFlatActions()[$actionName] ?? null))
                ) {
                    return $action;
                }
            }

            if (($component instanceof Action) || ($component instanceof ActionGroup)) {
                continue;
            }

            $componentKey = $component->getKey(isAbsolute: false);

            if (filled($componentKey)) {
                $componentInheritanceKey = $component->getInheritanceKey(isAbsolute: false);

                if (
                    filled($nestedContainerKey) &&
                    ($nestedContainerKey !== $componentKey) &&
                    filled($componentInheritanceKey) &&
                    (! str($nestedContainerKey)->startsWith("{$componentInheritanceKey}."))
                ) {
                    continue;
                }

                if ($nestedContainerKey === $componentKey) {
                    if ($action = $component->getAction($actionName)) {
                        return $action;
                    }

                    $componentNestedContainerKey = null;
                } elseif (filled($nestedContainerKey) && filled($componentInheritanceKey)) {
                    $componentNestedContainerKey = (string) str($nestedContainerKey)->after("{$componentInheritanceKey}.");
                } else {
                    $componentNestedContainerKey = $nestedContainerKey;
                }
            } else {
                $componentNestedContainerKey = $nestedContainerKey;
            }

            foreach ($component->getChildSchemas() as $childSchema) {
                $childSchemaName = $childSchema->getKey(isAbsolute: false);

                if (filled($childSchemaName)) {
                    if (blank($componentNestedContainerKey)) {
                        continue;
                    }

                    if (
                        ($componentNestedContainerKey !== $childSchemaName)
                        && (! str($componentNestedContainerKey)->startsWith("{$childSchemaName}."))
                    ) {
                        continue;
                    }

                    $childSchemaNestedContainerKey = ($componentNestedContainerKey === $childSchemaName)
                        ? null
                        : (string) str($componentNestedContainerKey)->after("{$childSchemaName}.");
                } else {
                    $childSchemaNestedContainerKey = $componentNestedContainerKey;
                }

                if ($action = $childSchema->getAction($actionName, $childSchemaNestedContainerKey)) {
                    return $action;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<Component>  $skipComponentsChildContainersWhileSearching
     */
    public function getComponent(string | Closure $findComponentUsing, bool $withActions = true, bool $withHidden = false, bool $isAbsoluteKey = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null
    {
        if (is_string($findComponentUsing) && (! $isAbsoluteKey) && filled($key = $this->getKey())) {
            $findComponentUsing = "{$key}.$findComponentUsing";
            $isAbsoluteKey = true;
        }

        if ($skipComponentsChildContainersWhileSearching) {
            foreach ($this->getComponents($withActions, $withHidden) as $component) {
                if ($findComponentUsing instanceof Closure) {
                    if ($findComponentUsing($component)) {
                        return $component;
                    }

                    if (in_array($component, $skipComponentsChildContainersWhileSearching, strict: true)) {
                        continue;
                    }

                    foreach ($component->getChildSchemas($withHidden) as $childSchema) {
                        if ($foundComponent = $childSchema->getComponent($findComponentUsing, $withActions, $withHidden, $isAbsoluteKey, $skipComponentsChildContainersWhileSearching)) {
                            return $foundComponent;
                        }
                    }

                    continue;
                }

                if (! ($component instanceof Component)) {
                    continue;
                }

                $componentKey = $component->getKey();

                if (filled($componentKey) && ($componentKey === $findComponentUsing)) {
                    return $component;
                }

                if (in_array($component, $skipComponentsChildContainersWhileSearching, strict: true)) {
                    continue;
                }

                $componentInheritanceKey = $component->getInheritanceKey();

                if (blank($componentInheritanceKey) || str_starts_with($findComponentUsing, "{$componentInheritanceKey}.")) {
                    foreach ($component->getChildSchemas($withHidden) as $childSchema) {
                        if ($foundComponent = $childSchema->getComponent($findComponentUsing, $withActions, $withHidden, $isAbsoluteKey, $skipComponentsChildContainersWhileSearching)) {
                            return $foundComponent;
                        }
                    }
                }
            }

            return null;
        }

        if (! is_string($findComponentUsing)) {
            return collect($this->getFlatComponents($withActions, $withHidden))->first($findComponentUsing);
        }

        return $this->getFlatComponents($withActions, $withHidden, withAbsoluteKeys: true)[$findComponentUsing] ?? null;
    }

    /**
     * @param  array<Component>  $skipComponentsChildContainersWhileSearching
     */
    public function getComponentByStatePath(string $statePath, bool $withHidden = false, bool $withAbsoluteStatePath = false, array $skipComponentsChildContainersWhileSearching = []): ?Component
    {
        if ((! $withAbsoluteStatePath) && filled($containerStatePath = $this->getStatePath())) {
            $statePath = "{$containerStatePath}.{$statePath}";
        }

        $search = function (self $container) use ($statePath, $withHidden, $skipComponentsChildContainersWhileSearching): ?Component {
            foreach ($container->getComponents(withActions: false, withHidden: $withHidden) as $component) {
                $componentStatePath = $component->getStatePath();

                if (filled($componentStatePath) && ($componentStatePath === $statePath)) {
                    return $component;
                }

                if (in_array($component, $skipComponentsChildContainersWhileSearching, strict: true)) {
                    continue;
                }

                if (blank($componentStatePath) || str_starts_with($statePath, "{$componentStatePath}.")) {
                    foreach ($component->getChildSchemas($withHidden) as $childSchema) {
                        if ($found = $childSchema->getComponentByStatePath($statePath, $withHidden, withAbsoluteStatePath: true, skipComponentsChildContainersWhileSearching: $skipComponentsChildContainersWhileSearching)) {
                            return $found;
                        }
                    }
                }
            }

            return null;
        };

        $skipIds = array_map('spl_object_id', $skipComponentsChildContainersWhileSearching);
        sort($skipIds);
        $cacheKey = $skipIds ? implode('-', $skipIds) : null;

        return $this->cachedComponentsByStatePath[$withHidden][$cacheKey][$statePath] ??= $search($this);
    }

    /**
     * @return array<Field>
     */
    public function getFlatFields(bool $withHidden = false, bool $withAbsoluteKeys = false): array
    {
        return collect($this->getFlatComponents(withActions: false, withHidden: $withHidden, withAbsoluteKeys: $withAbsoluteKeys))
            ->whereInstanceOf(Field::class)
            ->all();
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFlatComponents(bool $withActions = true, bool $withHidden = false, bool $withAbsoluteKeys = false, ?string $containerKey = null): array
    {
        $containerKey ??= $this->getKey();

        return $this->cachedFlatComponents[$withActions][$withHidden][$withAbsoluteKeys][$containerKey] ??= array_reduce(
            $this->getComponents($withActions, $withHidden),
            function (array $carry, Component | Action | ActionGroup $component) use ($containerKey, $withActions, $withHidden, $withAbsoluteKeys): array {
                if (($component instanceof Action) || ($component instanceof ActionGroup)) {
                    $carry[] = $component;

                    return $carry;
                }

                $componentKey = $component->getKey();

                if (blank($componentKey)) {
                    $carry[] = $component;
                } elseif ((! $withAbsoluteKeys) && filled($containerKey)) {
                    $carry[(string) str($componentKey)->after("{$containerKey}.")] = $component;
                } else {
                    $carry[$componentKey] = $component;
                }

                foreach ($component->getChildSchemas($withHidden) as $childSchema) {
                    $carry = [
                        ...$carry,
                        ...$childSchema->getFlatComponents($withActions, $withHidden, $withAbsoluteKeys, $containerKey),
                    ];
                }

                return $carry;
            },
            initial: [],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getComponents(bool $withActions = true, bool $withHidden = false, bool $withOriginalKeys = false): array
    {
        $allComponents = $this->cachedComponents ??= value(function (): array {
            $components = [];

            foreach (Arr::wrap($this->evaluate($this->components)) as $componentIndex => $component) {
                if ($component instanceof Action) {
                    $this->modifyAction($component);
                }

                if ($component instanceof ActionGroup) {
                    $this->modifyActionGroup($component);
                }

                if (($component instanceof Action) || ($component instanceof ActionGroup)) {
                    $component = $component->schemaContainer($this);
                    $components[$componentIndex] = $component;

                    continue;
                }

                if (is_string($component)) {
                    $component = Text::make($component);
                }

                if (! $component instanceof Component) {
                    $component = Html::make($component);
                }

                $component = $component->container($this);
                $components[$componentIndex] = $component;
            }

            return $components;
        });

        $components = array_filter(
            $allComponents,
            function (Component | Action | ActionGroup $component) use ($withActions, $withHidden): bool {
                if ((($component instanceof Action) || ($component instanceof ActionGroup)) && (! $withActions)) {
                    return false;
                }

                return $withHidden || ! $component->isHidden();
            }
        );

        if (! $withOriginalKeys) {
            $components = array_values($components);
        }

        return $components;
    }

    protected function cloneComponents(): static
    {
        if (! ($this->components instanceof Closure)) {
            $this->components = array_map(
                fn (Component | Action | ActionGroup | string | Htmlable $component): Component | Action | ActionGroup | string | Htmlable => match (true) {
                    $component instanceof Action, $component instanceof ActionGroup => (clone $component)
                        ->schemaContainer($this),
                    $component instanceof Component => $component
                        ->container($this)
                        ->getClone(),
                    default => $component,
                },
                Arr::wrap($this->components),
            );

            $this->cachedComponents = null;
            $this->cachedFlatComponents = [];
            $this->cachedComponentsByStatePath = [];
        }

        return $this;
    }
}
