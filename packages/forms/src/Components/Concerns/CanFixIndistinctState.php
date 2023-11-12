<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanDisableOptions;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CanFixIndistinctState
{
    public function fixIndistinctState(): static
    {
        $this->distinct();
        $this->live();

        $this->afterStateUpdated(static function (Component $component, mixed $state, Set $set) {
            if (blank($state)) {
                return;
            }

            $repeater = $component->getParentRepeater();

            if (! $repeater) {
                return;
            }

            $repeaterStatePath = $repeater->getStatePath();

            $componentItemStatePath = str($component->getStatePath())
                ->after("{$repeaterStatePath}.")
                ->before('.');

            $repeaterItemKey = (string) Str::of($component->getStatePath())
                ->after("{$repeaterStatePath}.")
                ->beforeLast(".{$componentItemStatePath}");

            $repeaterSiblingState = Arr::except($repeater->getState(), [$repeaterItemKey]);

            if (empty($repeaterSiblingState)) {
                return;
            }

            if (is_array($state)) {
                $repeaterSiblingState
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

            $repeaterSiblingState
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
