<?php

namespace Filament;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;

class FilamentManager
{
    protected bool $isNavigationMounted = false;

    protected array $navigationGroups = [];

    protected array $navigationItems = [];

    protected array $pages = [];

    protected array $resources = [];

    protected array $scripts = [];

    protected array $scriptData = [];

    protected array $styles = [];

    protected array $widgets = [];

    public function mountNavigation(): void
    {
        foreach (static::getPages() as $page) {
            $page::registerNavigationItems();
        }

        foreach (static::getResources() as $resource) {
            $resource::registerNavigationItems();
        }

        $this->isNavigationMounted = true;
    }

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
    }

    public function registerResources(array $resources): void
    {
        $this->resources = array_merge($this->resources, $resources);
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

    public function getAvatarUrl(FilamentUser $user): string
    {
        $avatar = null;

        if ($user instanceof HasAvatar) {
            $avatar = $user->getFilamentAvatarUrl();
        }

        if ($avatar) {
            return $avatar;
        }

        $provider = config('filament.default_avatar_provider');

        return (new $provider())->get($user);
    }

    public function getNavigation(): array
    {
        if (! $this->isNavigationMounted) {
            $this->mountNavigation();
        }

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
