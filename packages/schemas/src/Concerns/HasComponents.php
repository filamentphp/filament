<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasComponents
{
    /**
     * @var array<Component | Action | ActionGroup | string> | Component | Action | ActionGroup | string | Closure
     */
    protected array | Component | Action | ActionGroup | string | Closure $components = [];

    /**
     * @var array<array<array<array<array<string, Component| Action | ActionGroup>>>>>
     */
    protected array $cachedFlatComponents = [];

    /**
     * @param  array<Component | Action | ActionGroup | string> | Component | Action | ActionGroup | string | Closure  $components
     */
    public function components(array | Component | Action | ActionGroup | string | Closure $components): static
    {
        $this->components = $components;

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Component | Action | ActionGroup | string | Closure  $components
     */
    public function schema(array | Component | Action | ActionGroup | string | Closure $components): static
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
                if (blank($nestedContainerKey)) {
                    continue;
                }

                if (
                    ($nestedContainerKey !== $componentKey) &&
                    (! str($nestedContainerKey)->startsWith("{$componentKey}."))
                ) {
                    continue;
                }

                if ($nestedContainerKey === $componentKey) {
                    if ($action = $component->getAction($actionName)) {
                        return $action;
                    }

                    $componentNestedContainerKey = null;
                } else {
                    $componentNestedContainerKey = (string) str($nestedContainerKey)->after("{$componentKey}.");
                }
            } else {
                $componentNestedContainerKey = $nestedContainerKey;
            }

            foreach ($component->getChildSchemas() as $childSchema) {
                $childSchemaKey = $childSchema->getKey(isAbsolute: false);

                if (filled($childSchemaKey)) {
                    if (blank($componentNestedContainerKey)) {
                        continue;
                    }

                    if (
                        ($componentNestedContainerKey !== $childSchemaKey)
                        && (! str($componentNestedContainerKey)->startsWith("{$childSchemaKey}."))
                    ) {
                        continue;
                    }

                    $childSchemaNestedContainerKey = ($componentNestedContainerKey === $childSchemaKey)
                        ? null
                        : (string) str($componentNestedContainerKey)->after("{$childSchemaKey}.");
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

    public function getComponent(string | Closure $findComponentUsing, bool $withActions = true, bool $withHidden = false, bool $isAbsoluteKey = false, ?Component $skipComponentChildContainersWhileSearching = null): Component | Action | ActionGroup | null
    {
        if (is_string($findComponentUsing) && (! $isAbsoluteKey) && filled($key = $this->getKey())) {
            $findComponentUsing = "{$key}.$findComponentUsing";
            $isAbsoluteKey = true;
        }

        if ($skipComponentChildContainersWhileSearching) {
            foreach ($this->getComponents($withActions, $withHidden) as $component) {
                if ($findComponentUsing instanceof Closure) {
                    if ($findComponentUsing($component)) {
                        return $component;
                    }

                    if ($component === $skipComponentChildContainersWhileSearching) {
                        continue;
                    }

                    foreach ($component->getChildSchemas($withHidden) as $childSchema) {
                        if ($foundComponent = $childSchema->getComponent($findComponentUsing, $withActions, $withHidden, $isAbsoluteKey, skipComponentChildContainersWhileSearching: $skipComponentChildContainersWhileSearching)) {
                            return $foundComponent;
                        }
                    }

                    continue;
                }

                $componentKey = $component->getKey();

                if (filled($componentKey) && ($componentKey === $findComponentUsing)) {
                    return $component;
                }

                if ($component === $skipComponentChildContainersWhileSearching) {
                    continue;
                }

                if (blank($componentKey) || str_starts_with($findComponentUsing, "{$componentKey}.")) {
                    foreach ($component->getChildSchemas($withHidden) as $childSchema) {
                        if ($foundComponent = $childSchema->getComponent($findComponentUsing, $withActions, $withHidden, $isAbsoluteKey, $skipComponentChildContainersWhileSearching)) {
                            return $foundComponent;
                        }
                    }
                }
            }
        }

        if (! is_string($findComponentUsing)) {
            return collect($this->getFlatComponents($withActions, $withHidden))->first($findComponentUsing);
        }

        return $this->getFlatComponents($withActions, $withHidden, withAbsoluteKeys: true)[$findComponentUsing] ?? null;
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
     * @return array<Component | Action | ActionGroup | string>
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
     * @return array<Component | Action | ActionGroup | string>
     */
    public function getComponents(bool $withActions = true, bool $withHidden = false, bool $withOriginalKeys = false): array
    {
        $components = array_map(function (Component | Action | ActionGroup | string $component): Component | Action | ActionGroup {
            if ($component instanceof Action) {
                $this->configureAction($component);
            }

            if ($component instanceof ActionGroup) {
                $this->configureActionGroup($component);
            }

            if (($component instanceof Action) || ($component instanceof ActionGroup)) {
                return $component->schemaContainer($this);
            }

            if (is_string($component)) {
                $component = Text::make($component);
            }

            return $component->container($this);
        }, Arr::wrap($this->evaluate($this->components)));

        if ($withActions && $withHidden) {
            return $components;
        }

        return collect($components)
            ->filter(function (Component | Action | ActionGroup $component) use ($withActions, $withHidden) {
                if ((! $withActions) && (($component instanceof Action) || ($component instanceof ActionGroup))) {
                    return false;
                }

                if ((! $withHidden) && $component->isHidden()) {
                    return false;
                }

                return true;
            })
            ->when(
                ! $withOriginalKeys,
                fn (Collection $collection): Collection => $collection->values(),
            )
            ->all();
    }

    protected function cloneComponents(): static
    {
        if (! ($this->components instanceof Closure)) {
            $this->components = array_map(
                fn (Component | Action | ActionGroup | string $component): Component | Action | ActionGroup | string => match (true) {
                    $component instanceof Action, $component instanceof ActionGroup => (clone $component)
                        ->schemaContainer($this),
                    $component instanceof Component => $component
                        ->container($this)
                        ->getClone(),
                    default => $component,
                },
                Arr::wrap($this->components),
            );
        }

        return $this;
    }
}
