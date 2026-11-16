<?php

namespace Filament\Support\State;

use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use InvalidArgumentException;

class LazyLoadedState
{
    /**
     * @param  array<string, Column | Entry>  $components
     * @return array<string, string>
     */
    public function render(array $components): array
    {
        if (! $components) {
            return [];
        }

        $firstComponent = reset($components);
        $group = $firstComponent->getLazyLoadedStateGroup();

        if (! $group) {
            abort_unless(count($components) === 1, 403);

            return [array_key_first($components) => $firstComponent->renderLazyLoadedState()];
        }

        $state = $this->getGroupState($components);
        $html = [];

        foreach ($components as $identifier => $component) {
            $html[$identifier] = $component->renderLazyLoadedState(hasState: true, state: $state[$component->getLazyLoadedStateKey()]);
        }

        return $html;
    }

    /**
     * @param  array<string, Column | Entry>  $components
     * @return array<string, mixed>
     *
     * @internal
     */
    public function getGroupState(array $components): array
    {
        $firstComponent = reset($components);
        $group = $firstComponent->getLazyLoadedStateGroup();
        $groupName = $group->getName($firstComponent);
        $keyedComponents = [];
        $records = [];

        foreach ($components as $component) {
            abort_unless($component->getLazyLoadedStateGroup()?->getName($component) === $groupName, 403);

            $key = $component->getLazyLoadedStateKey();

            if (array_key_exists($key, $keyedComponents)) {
                throw new InvalidArgumentException("Lazy-loaded state group [{$groupName}] has duplicate key [{$key}].");
            }

            $keyedComponents[$key] = $component;
            $records[$key] = $component->getRecord();
        }

        $state = $group->getState($firstComponent, $keyedComponents, $records);

        foreach ($keyedComponents as $key => $component) {
            if (! array_key_exists($key, $state)) {
                throw new InvalidArgumentException("Lazy-loaded state group [{$groupName}] did not return key [{$key}].");
            }
        }

        return $state;
    }
}
