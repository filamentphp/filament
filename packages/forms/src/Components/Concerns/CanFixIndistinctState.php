<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Set;
use Illuminate\Support\Arr;

trait CanFixIndistinctState
{
    public function fixIndistinctState(bool | Closure $condition = true): static
    {
        $this->distinct($condition);
        $this->live(condition: $condition);

        $this->afterStateUpdated(static function (Component $component, mixed $state, Set $set) use ($condition) {
            if (! $component->evaluate($condition)) {
                return;
            }

            if (blank($state)) {
                return;
            }

            $repeater = $component->getParentRepeater();

            if (! $repeater) {
                return;
            }

            $repeaterStatePath = $repeater->getStatePath();

            $componentItemStatePath = (string) str($component->getStatePath())
                ->after("{$repeaterStatePath}.")
                ->after('.');

            $repeaterItemKey = (string) str($component->getStatePath())
                ->after("{$repeaterStatePath}.")
                ->beforeLast(".{$componentItemStatePath}");

            $repeaterSiblingState = Arr::except($repeater->getState(), [$repeaterItemKey]);

            if (empty($repeaterSiblingState)) {
                return;
            }

            if (is_array($state)) {
                collect($repeaterSiblingState)
                    ->filter(fn (array $itemState): bool => filled(array_intersect(data_get($itemState, $componentItemStatePath, []), $state)))
                    ->map(fn (array $itemState): array => collect(data_get($itemState, $componentItemStatePath) ?? [])
                        ->diff($state)
                        ->values()
                        ->all())
                    ->each(fn (array $newSiblingItemState, string $itemKey) => $set(
                        path: "{$repeaterStatePath}.{$itemKey}.{$componentItemStatePath}",
                        state: $newSiblingItemState,
                        isAbsolute: true,
                    ));

                return;
            }

            collect($repeaterSiblingState)
                ->map(fn (array $itemState): mixed => data_get($itemState, $componentItemStatePath))
                ->filter(function (mixed $siblingItemComponentState) use ($state): bool {
                    if ($siblingItemComponentState === false) {
                        return false;
                    }

                    if (blank($siblingItemComponentState)) {
                        return false;
                    }

                    return $siblingItemComponentState === $state;
                })
                ->each(fn (mixed $siblingItemComponentState, string $itemKey) => $set(
                    path: "{$repeaterStatePath}.{$itemKey}.{$componentItemStatePath}",
                    state: match ($siblingItemComponentState) {
                        true => false,
                        default => null,
                    },
                    isAbsolute: true,
                ));
        });

        return $this;
    }
}
