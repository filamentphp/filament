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

        $this->disableOptionWhen(static function (Component & CanDisableOptions $component, string $value, mixed $state) {
            $repeater = $component->getParentRepeater();

            if (! $repeater) {
                return false;
            }

            return collect($repeater->getRawState())
                ->pluck(
                    (string) str($component->getStatePath())
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
                ->diff(Arr::wrap(($state instanceof BackedEnum) ? $state->value : $state))
                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                ->contains($value);
        });

        return $this;
    }
}
