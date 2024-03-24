<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationManager;

trait HasNavigation
{
    /**
     * @var array<string | int, NavigationGroup | string>
     */
    protected array $navigationGroups = [];

    /**
     * @var array<NavigationItem>
     */
    protected array $navigationItems = [];

    protected Closure | bool $navigationBuilder = true;

    protected ?NavigationManager $navigationManager = null;

    public function navigation(Closure | bool $builder = true): static
    {
        $this->navigationBuilder = $builder;

        return $this;
    }

    /**
     * @return array<NavigationGroup>
     */
    public function buildNavigation(): array
    {
        /** @var NavigationBuilder $builder */
        $builder = app()->call($this->navigationBuilder);

        return $builder->getNavigation();
    }

    /**
     * @param  array<string | int, NavigationGroup | string>  $groups
     */
    public function navigationGroups(array $groups): static
    {
        if (isset($this->navigationManager)) {
            $this->navigationManager->navigationGroups($groups);

            return $this;
        }

        $this->navigationGroups = [
            ...$this->navigationGroups,
            ...$groups,
        ];

        return $this;
    }

    /**
     * @param  array<NavigationItem>  $items
     */
    public function navigationItems(array $items): static
    {
        if (isset($this->navigationManager)) {
            $this->navigationManager->navigationItems($items);

            return $this;
        }

        $this->navigationItems = [
            ...$this->navigationItems,
            ...$items,
        ];

        return $this;
    }

    public function hasNavigation(): bool
    {
        return $this->navigationBuilder !== false;
    }

    public function hasNavigationBuilder(): bool
    {
        return $this->navigationBuilder instanceof Closure;
    }

    /**
     * @return array<NavigationGroup>
     */
    public function getNavigation(): array
    {
        $this->navigationManager = app(NavigationManager::class);

        try {
            return app(NavigationManager::class)->get();
        } finally {
            $this->navigationManager = null;
        }
    }

    /**
     * @return array<string | int, NavigationGroup | string>
     */
    public function getNavigationGroups(): array
    {
        return $this->navigationGroups;
    }

    /**
     * @return array<NavigationItem>
     */
    public function getNavigationItems(): array
    {
        return $this->navigationItems;
    }
}
