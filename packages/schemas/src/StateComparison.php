<?php

namespace Filament\Schemas;

use Filament\Support\Components\Component;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @implements Arrayable<string, mixed>
 */
class StateComparison extends Component implements Arrayable
{
    /**
     * @var array<string, mixed>
     */
    protected array $base = [];

    /**
     * @var array<string, mixed>
     */
    protected array $draft = [];

    /**
     * @var array<string, mixed>
     */
    protected array $current = [];

    /**
     * @var array<string>
     */
    protected array $ignoredStatePaths = [];

    /**
     * @var array<string>
     */
    protected array $orderedStatePaths = [];

    /**
     * @var array<string, array<string>> | null
     */
    protected ?array $statePathGroups = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<string, mixed>  $base
     */
    public function base(array $base): static
    {
        $this->base = $base;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $draft
     */
    public function draft(array $draft): static
    {
        $this->draft = $draft;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $current
     */
    public function current(array $current): static
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @param  array<string>  $ignoredStatePaths
     */
    public function ignoredStatePaths(array $ignoredStatePaths): static
    {
        $this->ignoredStatePaths = $ignoredStatePaths;

        return $this;
    }

    /**
     * @param  array<string, array<string>> | null  $statePathGroups
     */
    public function statePathGroups(?array $statePathGroups): static
    {
        $this->statePathGroups = $statePathGroups;

        return $this;
    }

    /**
     * @param  array<string>  $orderedStatePaths
     */
    public function orderedStatePaths(array $orderedStatePaths): static
    {
        $this->orderedStatePaths = $orderedStatePaths;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getOrderedStatePaths(): array
    {
        return $this->orderedStatePaths;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getStatePathGroups(): array
    {
        if ($this->statePathGroups !== null) {
            return $this->statePathGroups;
        }

        $groups = [];

        foreach (array_unique([...array_keys($this->base), ...array_keys($this->draft), ...array_keys($this->current)]) as $path) {
            $groups[$path] = [(string) $path];
        }

        return $groups;
    }

    /**
     * @return array<string>
     */
    public function getConflictingPaths(): array
    {
        $paths = [];

        foreach ($this->getStatePathGroups() as $path => $statePaths) {
            $base = $this->getComparableState($this->base, $statePaths);
            $draft = $this->getComparableState($this->draft, $statePaths);
            $current = $this->getComparableState($this->current, $statePaths);

            if (($base !== $draft) && ($base !== $current) && ($draft !== $current)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    public function hasConflicts(): bool
    {
        return $this->getConflictingPaths() !== [];
    }

    /**
     * @return array<string>
     */
    public function getIncomingPaths(): array
    {
        $paths = [];

        foreach ($this->getStatePathGroups() as $path => $statePaths) {
            $base = $this->getComparableState($this->base, $statePaths);

            if (($base === $this->getComparableState($this->draft, $statePaths)) && ($base !== $this->getComparableState($this->current, $statePaths))) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * @param  array<string, mixed>  $state
     * @param  array<string>  $statePaths
     * @return array<string, mixed>
     */
    protected function getComparableState(array $state, array $statePaths): array
    {
        $comparableState = [];

        foreach ($statePaths as $statePath) {
            if ($this->isIgnoredStatePath($statePath) || (! Arr::has($state, $statePath))) {
                continue;
            }

            $comparableState[$statePath] = $this->normalizeStateForComparison(data_get($state, $statePath), $statePath);
        }

        ksort($comparableState);

        return $comparableState;
    }

    protected function normalizeStateForComparison(mixed $state, string $statePath): mixed
    {
        if (! is_array($state)) {
            return $state;
        }

        foreach ($state as $key => $value) {
            $childStatePath = "{$statePath}.{$key}";

            if ($this->isIgnoredStatePath($childStatePath)) {
                unset($state[$key]);

                continue;
            }

            $state[$key] = $this->normalizeStateForComparison($value, $childStatePath);
        }

        if (! Str::is($this->orderedStatePaths, $statePath)) {
            ksort($state);
        }

        return $state;
    }

    protected function isIgnoredStatePath(string $statePath): bool
    {
        foreach ($this->ignoredStatePaths as $ignoredStatePath) {
            if (Str::is($ignoredStatePath, $statePath) || Str::is("{$ignoredStatePath}.*", $statePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBase(): array
    {
        return $this->base;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDraft(): array
    {
        return $this->draft;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCurrent(): array
    {
        return $this->current;
    }

    /**
     * @return array<string>
     */
    public function getIgnoredStatePaths(): array
    {
        return $this->ignoredStatePaths;
    }

    /**
     * @return array<string>
     */
    public function getChangedPaths(): array
    {
        $base = Arr::dot($this->base);
        $current = Arr::dot($this->current);
        $changedPaths = [];

        foreach (array_unique([...array_keys($base), ...array_keys($current)]) as $path) {
            if ($this->isIgnoredStatePath($path)) {
                continue;
            }

            if (
                (array_key_exists($path, $base) !== array_key_exists($path, $current)) ||
                (($base[$path] ?? null) !== ($current[$path] ?? null))
            ) {
                $changedPaths[] = $path;
            }
        }

        // Flattened values do not reveal changes to significant key ordering.
        foreach ($this->getStatePathGroups() as $statePaths) {
            foreach ($statePaths as $path) {
                if ($this->getComparableState($this->base, [$path]) === $this->getComparableState($this->current, [$path])) {
                    continue;
                }

                foreach ($changedPaths as $changedPath) {
                    if (($changedPath === $path) || str_starts_with($changedPath, "{$path}.")) {
                        continue 2;
                    }
                }

                $changedPaths[] = $path;
            }
        }

        return array_values(array_unique($changedPaths));
    }

    public function isStale(): bool
    {
        return $this->getChangedPaths() !== [];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'base' => $this->getBase(),
            'draft' => $this->getDraft(),
            'current' => $this->getCurrent(),
            'changedPaths' => $this->getChangedPaths(),
            'conflictingPaths' => $this->getConflictingPaths(),
            'incomingPaths' => $this->getIncomingPaths(),
            'statePathGroups' => $this->getStatePathGroups(),
            'orderedStatePaths' => $this->getOrderedStatePaths(),
            'ignoredStatePaths' => $this->getIgnoredStatePaths(),
        ];
    }
}
