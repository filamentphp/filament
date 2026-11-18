<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Filament\Forms\Components\Contracts\CanDisableOptions;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Arr;

trait CanDisableOptionsWhenSelectedInSiblingRepeaterItems
{
    public function disableOptionsWhenSelectedInSiblingRepeaterItems(): static
    {
        $this->distinct();
        $this->live();

        // The callback runs once for each option, but the values selected in sibling items only change
        // when the repeater's state does. Each item's field (one clone each) remembers them by its state path.
        $siblingItemValuesByStatePath = [];

        $this->disableOptionWhen(static function (Component & CanDisableOptions $component, string $value) use (&$siblingItemValuesByStatePath): bool {
            $repeater = $component->getParentRepeater();

            if (! $repeater) {
                return false;
            }

            $repeaterState = $repeater->getRawState();
            $state = $component->getRawState();
            $statePath = $component->getStatePath();
            $cached = $siblingItemValuesByStatePath[$statePath] ?? null;

            if (($cached === null) || ($cached['repeaterState'] !== $repeaterState) || ($cached['state'] !== $state)) {
                $cached = $siblingItemValuesByStatePath[$statePath] = [
                    'repeaterState' => $repeaterState,
                    'state' => $state,
                    'values' => collect($repeaterState)
                        ->pluck(
                            (string) str($statePath)
                                ->after("{$repeater->getStatePath()}.")
                                ->after('.'),
                        )
                        ->flatten()
                        ->map(function (mixed $siblingItemState): mixed {
                            if ($siblingItemState instanceof BackedEnum) {
                                return $siblingItemState->value;
                            }

                            return $siblingItemState;
                        })
                        ->diff(array_map(
                            static fn (mixed $value): mixed => ($value instanceof BackedEnum) ? $value->value : $value,
                            Arr::wrap($state),
                        ))
                        ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                        ->all(),
                ];
            }

            return in_array($value, $cached['values']);
        });

        return $this;
    }
}
