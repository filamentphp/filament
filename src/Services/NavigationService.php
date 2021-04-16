<?php

namespace Filament\Services;

use Filament\Filament;
use Filament\NavigationItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class NavigationService
 * @package Filament\Services
 */
class NavigationService
{
    /**
     * @var \Illuminate\Support\Collection
     */
    public Collection $items;

    /**
     * NavigationService constructor.
     */
    public function __construct()
    {
        $this->items = collect();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getItems($all = false): Collection
    {
        if ($all) {
            $this->withDashboard();
            $this->withResources();
            $this->withPages();
            //$this->withCustom();
        }

        $this->items = $this->items->sortBy(fn ($item) => $item->sort)->values();
        return $this->items;
    }

    /**
     * Navigation items for the dashboard
     * @return NavigationService
     */
    public function withDashboard(): NavigationService
    {
        $this->items->push(
            NavigationItem::make('filament::dashboard.title', route('dashboard'))
                ->activeRule(
                  (string) Str::of(route('dashboard', [], false))->after('/')
                )
                ->icon('heroicon-o-home')
                ->sort(-1),
        );

        return $this;
    }

    /**
     * Navigation items for all resources
     * @return NavigationService
     */
    public function withResources(): NavigationService
    {
        foreach (Filament::getResources() as $resource) {
            if (Filament::can('viewAny', $resource::getModel())) {
                $this->items->push(...$resource::navigationItems());
            }
        }

        return $this;
    }

    /**
     * Navigation items for all pages
     * @return NavigationService
     */
    public function withPages(): NavigationService
    {
        foreach (Filament::getPages() as $page) {
            if (Filament::can('view', $page)) {
                $this->items->push(...$page::navigationItems());
            }
        }

        return $this;
    }

    /**
     * Navigation items for any custom sections
     * @return NavigationService
     */
    public function withCustom(): NavigationService
    {
        return $this;
    }
}
