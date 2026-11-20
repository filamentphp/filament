<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Schemas\StatePathSegment;
use Illuminate\Support\Str;

trait CanGenerateUuids
{
    protected Closure | bool | null $generateUuidUsing = null;

    public function generateUuidUsing(Closure | bool | null $callback): static
    {
        $this->generateUuidUsing = $callback;

        return $this;
    }

    public function generateUuid(): ?string
    {
        if ($this->generateUuidUsing instanceof Closure) {
            return $this->evaluate($this->generateUuidUsing);
        }

        if ($this->generateUuidUsing === false) {
            return null;
        }

        return (string) Str::uuid();
    }

    /**
     * @param  array<string | int, mixed>  $items
     * @return array<string | int, mixed>
     */
    protected function rekeyItemsWithUnsafeKeys(array $items): array
    {
        $itemsWithSafeKeys = [];
        $nextNumericItemKey = 0;

        foreach ($items as $itemKey => $item) {
            if (StatePathSegment::isSafe($itemKey)) {
                $itemsWithSafeKeys[$itemKey] = $item;

                continue;
            }

            $itemKey = $this->generateSafeReplacementItemKey($items, $itemsWithSafeKeys, $nextNumericItemKey);

            $itemsWithSafeKeys[$itemKey] = $item;
        }

        return $itemsWithSafeKeys;
    }

    /**
     * @param  array<string | int, mixed>  $items
     * @param  array<string | int, mixed>  $itemsWithSafeKeys
     */
    protected function generateSafeReplacementItemKey(array $items, array $itemsWithSafeKeys, int &$nextNumericItemKey): string | int
    {
        $itemKey = $this->generateUuid();

        if ($itemKey === null) {
            while ($this->hasItemKey($nextNumericItemKey, $items, $itemsWithSafeKeys)) {
                $nextNumericItemKey++;
            }

            return $nextNumericItemKey++;
        }

        if (StatePathSegment::isSafe($itemKey) && (! $this->hasItemKey($itemKey, $items, $itemsWithSafeKeys))) {
            return $itemKey;
        }

        do {
            $itemKey = (string) Str::uuid();
        } while ($this->hasItemKey($itemKey, $items, $itemsWithSafeKeys));

        return $itemKey;
    }

    /**
     * @param  array<string | int, mixed>  $items
     * @param  array<string | int, mixed>  $itemsWithSafeKeys
     */
    protected function hasItemKey(string | int $itemKey, array $items, array $itemsWithSafeKeys): bool
    {
        return array_key_exists($itemKey, $items) || array_key_exists($itemKey, $itemsWithSafeKeys);
    }

    public static function fake(): Closure
    {
        return static::configureUsing(
            fn ($component) => $component->generateUuidUsing(false),
        );
    }
}
