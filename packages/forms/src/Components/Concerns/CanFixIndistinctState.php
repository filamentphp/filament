<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Arr;

trait CanFixIndistinctState
{
    public function fixIndistinctState(bool | Closure $condition = true): static
    {
        $this->distinct($condition);
        $this->live(condition: $condition);

        $this->afterStateUpdated(static function (Component $component, mixed $state, Get $get, Set $set) use ($condition): void {
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

            $repeaterSiblingState = Arr::except($repeater->getRawState(), [$repeaterItemKey]);

            if (empty($repeaterSiblingState)) {
                return;
            }

            if (is_array($state)) {
                foreach (array_keys($repeaterSiblingState) as $itemKey) {
                    $siblingItemStatePath = "{$repeaterStatePath}.{$itemKey}.{$componentItemStatePath}";

                    $siblingItemComponentState = (array) $get(
                        path: $siblingItemStatePath,
                        isAbsolute: true,
                    );

                    $newSiblingItemState = array_filter(
                        $siblingItemComponentState,
                        static fn (mixed $siblingItemComponentStateValue): bool => ! in_array($siblingItemComponentStateValue, $state, strict: true),
                    );

                    if (count($newSiblingItemState) === count($siblingItemComponentState)) {
                        continue;
                    }

                    $set(
                        path: $siblingItemStatePath,
                        state: array_values($newSiblingItemState),
                        isAbsolute: true,
                    );
                }

                return;
            }

            collect($repeaterSiblingState)
                ->map(fn (array $itemState, string $itemKey): mixed => $get(
                    path: "{$repeaterStatePath}.{$itemKey}.{$componentItemStatePath}",
                    isAbsolute: true,
                ))
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
