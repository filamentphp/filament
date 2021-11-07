<?php

namespace Filament;

class FilamentManager
{
    protected array $navigationGroups = [];

    protected array $navigationItems = [];

    protected array $pages = [];

    protected array $resources = [];

    protected array $scripts = [];

    protected array $scriptData = [];

    protected array $styles = [];

    protected array $widgets = [];

    public function registerNavigationGroups(array $groups): void
    {
        $this->navigationGroups = array_merge($this->navigationGroups, $groups);
    }

    public function registerNavigationItems(array $items): void
    {
        $this->navigationItems = array_merge($this->navigationItems, $items);
    }

    public function registerPages(array $pages): void
    {
        $this->pages = array_merge($this->pages, $pages);

        foreach ($pages as $page) {
            $page::registerNavigationItems();
        }
    }

    public function registerResources(array $resources): void
    {
        $this->resources = array_merge($this->resources, $resources);

        foreach ($resources as $resource) {
            $resource::registerNavigationItems();
        }
    }

    public function registerScripts(array $scripts): void
    {
        $this->scripts = array_merge($this->scripts, $scripts);
    }

    public function registerScriptData(array $data): void
    {
        $this->scriptData = array_merge($this->scriptData, $data);
    }

    public function registerStyles(array $styles): void
    {
        $this->styles = array_merge($this->styles, $styles);
    }

    public function registerWidgets(array $widgets): void
    {
        $this->widgets = array_merge($this->widgets, $widgets);
    }

    public function getNavigation(): array
    {
        $groupedItems = collect($this->navigationItems)
            ->sortBy(fn (NavigationItem $item): int => $item->getSort())
            ->groupBy(fn (NavigationItem $item): ?string => $item->getGroup());

        $sortedGroups = $groupedItems
            ->keys()
            ->sortBy(function (?string $group): int {
                if (! $group) {
                    return -1;
                }

                $sort = array_search($group, $this->navigationGroups);

                if ($sort === false) {
                    return count($this->navigationGroups);
                }

                return $sort;
            });

        return $sortedGroups
            ->mapWithKeys(function (?string $group) use ($groupedItems): array {
                return [$group => $groupedItems->get($group)];
            })
            ->toArray();
    }

    public function getNavigationGroups(): array
    {
        return $this->navigationGroups;
    }

    public function getNavigationItems(): array
    {
        return $this->navigationItems;
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function getScriptData(): array
    {
        return $this->scriptData;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getWidgets(): array
    {
        return $this->widgets;
    }
}
