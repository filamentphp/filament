<?php

namespace Filament\View;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NavigationGroup
{
    public static $groupsMap = [];

    public string $label;
    public string $mapKey;

    public string $icon = 'heroicon-o-collection';

    public int $sort = 0;

    public Collection $items;

    private function __construct(
        string $groupName,
        string $mapKey,
        ?string $icon = null,
        ?int $sort = null
    ) {
        $this->label = $groupName;
        $this->mapKey = $mapKey;
        if (null !== $icon) {
            $this->icon = $icon;
        }
        if (null !== $sort) {
            $this->sort = $sort;
        }
        $this->items = new Collection();
    }

    private static function make(
        string $groupName,
        ?string $mapKey = null,
        ?string $icon = null,
        ?int $sort = null
    ) {
        if (null === $mapKey) {
            $mapKey = Str::kebab($groupName);
        }
        if (!isset(static::$groupsMap[$mapKey])) {
            static::$groupsMap[$mapKey] = new self($groupName, $mapKey, $icon, $sort);
        }
        return static::$groupsMap[$mapKey];
    }

    /**
     * @param string $groupKey
     * @param array{groupName: string, icon?: string, ?sort: int}  $groupSettings
     *
     * @return static
     */
    public static function registerGroup(string $groupKey, array $groupSettings): self
    {
        extract($groupSettings, EXTR_SKIP);
        return static::make($groupName, $groupKey, $icon ?? null, $sort ?? null);
    }

    public static function group(string $groupName): self
    {
        return static::make($groupName);
    }

    public static function groups(): array
    {
        return static::$groupsMap;
    }

    // Instance methods
    public function push(...$values)
    {
        $this->items->push(...$values);

        return $this;
    }

    public function activeRule()
    {
        return $this->items->map(fn($item) => $item->activeRule)->toArray();
    }
}
