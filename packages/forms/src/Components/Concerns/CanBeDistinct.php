<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CanBeDistinct
{
    protected bool | Closure $isDistinct = false;

    public function distinct(bool | Closure $condition = true, bool $shouldDisableOptions = false): static
    {
        $this->isDistinct = $condition;

        // In shouldDisableOptions mode, any option(s) selected in one instance of the field will be
        // disabled in all other instances, using disableOptionWhen().  This really only makes sense for
        // Select, and not for Radio, Toggle or Checkbox.  So in this code, Select can work in either mode,
        // the rest will ignore shouldDisableOptions.
        //
        // When not in shouldDisableOptions mode, any option(s) selected in one instance of the field
        // will be de-selected in all other instances.  This requires setting the field to be live(), and
        // handling state with afterStateUpdated() and $set.

        if ($this instanceof Select && $shouldDisableOptions) {
            $this->disableOptionWhen(static function (Component $component, string $value, mixed $state) {
                if (! $component->isRepeated()) {
                    return false;
                }

                // collect the state of all other instances of this field, and check if this value already
                // exists. Using flatten() and wrap() means this will work for single or multiple select.
                return collect($component->getRepeaterComponent()?->getState())
                    ->pluck($component->getName())
                    ->flatten()
                    ->diff(Arr::wrap($state))
                    ->filter()
                    ->contains($value);
            });
        } else {
            $this->live();

            $this->afterStateUpdated(static function (Component $component, mixed $state, Set $set) {
                $repeaterPath = $component->getRepeaterComponent()?->getStatePath();

                $repeatKey = (string) Str::of($component->getStatePath())
                    ->beforeLast(".{$component->getName()}")
                    ->afterLast('.');

                $siblings = collect($component->getRepeaterComponent()?->getState())
                    ->reject(fn (array $item, string $key): bool => $key === $repeatKey);

                if (is_array($state)) {
                    // get the repeat keys of any other instances of this field with options that intersect with $state and
                    // set them to the diff (so remove the intersections)
                    $siblings->filter(fn (array $item): bool => ! empty(array_intersect(data_get($item, $component->getName(), []), $state)))
                        ->mapWithKeys(fn (array $item, string $key): array => [$key => array_values(array_diff(data_get($item, $component->getName(), []), $state))])
                        ->each(function (array $newState, string $key) use ($set, $repeaterPath, $component) {
                            $set(path: "{$repeaterPath}.{$key}.{$component->getName()}", state: $newState, isAbsolute: true);
                        });
                } else {
                    // get the repeat keys of any other instances of this field with the same $state and set them to null.
                    // @TODO figure out if we need to make the 'null' value configurable, like should it be 'false' for
                    // Toggles, or use a specified default(), etc?
                    $siblings->filter(fn (array $item): bool => data_get($item, $component->getName()) === $state)
                        ->map(fn (array $item, string $key): string => $key)
                        ->each(function (string $key) use ($set, $repeaterPath, $component) {
                            $set(path: "{$repeaterPath}.{$key}.{$component->getName()}", state: null, isAbsolute: true);
                        });
                }
            });
        }

        $this->rule(static function (Component $component, mixed $state) {
            return function (string $attribute, $value, Closure $fail) use ($component, $state) {
                if (! $component->isRepeated()) {
                    return;
                }

                $repeatKey = (string) Str::of($component->getStatePath())
                    ->beforeLast(".{$component->getName()}")
                    ->afterLast('.');

                $siblings = collect($component->getRepeaterComponent()?->getState())
                    ->reject(fn (array $item, string $key): bool => $key === $repeatKey);

                if (count($siblings) === 0) {
                    return;
                }

                if (is_bool($state)) {
                    // if it's boolean, it's a Toggle or Checkbox, so "one, and only one, must be selected"
                    $selected = collect($siblings)
                        ->pluck($component->getName())
                        ->contains(true);

                    if (empty($selected)) {
                        $fail("One of {$component->getLabel()} must be selected.");

                        return;
                    }

                    if ($value) {
                        $isDuplicate = $siblings->pluck($component->getName())
                            ->contains(true);

                        if ($isDuplicate) {
                            $fail("Only one of {$component->getLabel()} may be selected.");
                        }
                    }
                } elseif (is_array($state)) {
                    // an array is Select multiple() or CheckboxList, so test for any intersection with other instances
                    $intersects = $siblings->filter(fn (array $item): bool => ! empty(array_intersect(data_get($item, $component->getName(), []), $state)))
                        ->isNotEmpty();

                    if ($intersects) {
                        $fail("{$component->getLabel()} field must be distinct.");
                    }
                } else {
                    // it's a single select of some sort, so test for duplicates
                    $isDuplicate = $siblings->pluck($component->getName())
                        ->contains($state);

                    if ($isDuplicate) {
                        $fail("{$component->getLabel()} field must be distinct.");
                    }
                }
            };
        });

        return $this;
    }

    public function isDistinct(): bool
    {
        return (bool) $this->evaluate($this->isDistinct);
    }
}
