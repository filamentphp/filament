<?php

namespace Filament\View;

use Filament\View\Concerns\IsGroupItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NavigationGroup
{
    public static $menuMap = [];
    public static $groupsMap = [];

    public string $label;
    public string $mapKey;

    public string $icon = 'heroicon-o-collection';

    public int $sort = 0;

    public bool $isResourceGroup = false;
    public ?string $url = null;
    public ?string $parentActiveRule = null;
    public ?array $menus = null;

    public Collection $items;

    private function __construct(
        string $groupName,
        string $mapKey,
        ?IsGroupItem $resource = null
    ) {
        $this->label = $groupName;
        $this->mapKey = $mapKey;
        if (null !== $resource) {
            $this->isResourceGroup = true;
            $this->url = $resource::generateUrl();
            $this->parentActiveRule = $resource::defaultActiveRule();
            $this->menus = $resource::getGroupMenusList();
        }
        $this->items = new Collection();
    }

    public static function make(
        string $groupName,
        ?string $menuName = 'default',
        ?IsGroupItem $resource = null
    ) {
        $mapKey = Str::kebab($groupName);
        if (!isset(static::$groupsMap[$mapKey])) {
            static::$groupsMap[$mapKey] = new self($groupName, $mapKey, $resource);
        }
        if (!isset(static::$menuMap[$menuName])) {
            static::$menuMap[$menuName] = [];
        }
        if (!isset(static::$menuMap[$menuName][$mapKey])) {
            static::$menuMap[$menuName][$mapKey] = &static::$groupsMap[$mapKey];
        }
        return static::$groupsMap[$mapKey];
    }

    /**
     * @param string $groupKey
     * @param array{name: string, icon?: string, sort?: int}  $groupSettings
     *
     * @return static
     */
    public static function registerGroup(array $groupSettings): self
    {
        extract($groupSettings, EXTR_SKIP);
        /**
         * @var string $name
         * @var ?string $menuName
         * @var ?string $icon
         * @var ?string $sort
         * @var ?IsGroupItem $resource
         */
        if (isset($resource, $menuName)) {
            return static::make($name, $menuName, $resource)->setIcon($icon ?? null)->setSort($sort ?? null);
        }
        if (isset($resource)) {
            return static::make($name, 'default', $resource)->setIcon($icon ?? null)->setSort($sort ?? null);
        }
        if (isset($menuName)) {
            return static::make($name, $menuName)->setIcon($icon ?? null)->setSort($sort ?? null);
        }
        return static::make($name)->setIcon($icon ?? null)->setSort($sort ?? null);
    }

    /**
     * @param array $groupSettings
     *
     * @return array
     */
    public static function registerGroupWithMenus(array $groupSettings)
    {
        $groupMenus = $groupSettings['menus'];
        unset($groupSettings['menus']);
        $firstMenuName = array_shift($groupMenus);
        $groupSettings['menuName'] = $firstMenuName;

        // Register it once with full settings.
        self::registerGroup($groupSettings);

        // Then iterate thru each additional menu to register the group by name only.
        foreach ($groupMenus as $menuName) {
            self::addGroupToMenu($groupSettings['name'], $menuName);
        }
    }

    public static function addGroupToMenu(string $groupName, string $menuName): self
    {
        return static::make($groupName, null, null, $menuName);
    }

    public static function group(string $groupName): self
    {
        return static::make($groupName);
    }

    public static function menuGroups(?string $menuName = 'default'): array
    {
        return static::$menuMap[$menuName] ?? [];
    }

    public static function allGroups(): array
    {
        return static::$groupsMap;
    }

    public static function registeredMenuNames(): array
    {
        return array_keys(static::$menuMap);
    }

    // Instance methods
    public function push(...$values)
    {
        $this->items->push(...$values);

        return $this;
    }

    public function getActiveRule()
    {
        return $this->items->map(fn($item) => $item->activeRule)->toArray();
    }

    public function setSort(?int $sort)
    {
        if (null !== $sort) {
            $this->sort = $sort;
        }
        return $this;
    }


    public function setIcon(?string $icon)
    {
        if (null !== $icon) {
            $this->icon = $icon;
        }
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
