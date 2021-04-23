<?php

namespace Filament\View\Components;

use Filament\Filament;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\View\Concerns\IsGroupItem;
use Filament\View\NavigationGroup;
use Filament\View\NavigationItem;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Nav extends Component
{
    public $navName = 'default';
    public $items;

    public function __construct()
    {
        $this->items = collect();

        $this->items->push(
            NavigationItem::make('filament::dashboard.title', route('filament.dashboard'))
                ->activeRule(
                    (string) Str::of(route('filament.dashboard', [], false))->after('/')
                )
                ->icon('heroicon-o-home')
                ->sort(-1),
        );

        // Collect both types
        $appResources = collect(Filament::getResources());
        $appPages = collect(Filament::getPages());

        // Register both pages/resources that implement IsGroupItem
        $resourcesAsGroups = $appResources->lazy()->filter(fn($resource) => (new $resource) instanceof IsGroupItem);
        $resourcesAsGroups->each(static function ($resource) {
            if (Filament::can('viewAny', $resource::getModel())) {
                /**
                 * @var Resource|IsGroupItem $resource
                 */
                $resource::registerNavigationGroup();
            }
        });
        $pagesAsGroups = $appPages->lazy()->filter(fn($resource) => (new $resource) instanceof IsGroupItem);
        $pagesAsGroups->each(static function ($page) {
            /**
             * @var Page|IsGroupItem $page
             */
            if (Filament::can('view', $page)) {
                $page::registerNavigationGroup();
            }
        });

        // Register the regular pages/resources
        $regularResources = $appResources->lazy()->filter(fn($resource) => !((new $resource) instanceof IsGroupItem));
        $regularResources->each(function ($resource) {
            if (Filament::can('viewAny', $resource::getModel())) {
                if ($resource::hasNavigationGroup()) {
                    // For navigation groups we push the item into the groups items list.
                    NavigationGroup::group($resource::$navigationGroup)->push(...$resource::navigationItems());
                } else {
                    $this->items->push(...$resource::navigationItems());
                }
            }
        });
        $regularPages = $appPages->lazy()->filter(fn($resource) => !((new $resource) instanceof IsGroupItem));
        $regularPages->each(function ($page) {
            if (Filament::can('view', $page)) {
                if ($page::hasNavigationGroup()) {
                    // For navigation groups we push the item into the groups items list.
                    NavigationGroup::group($page::$navigationGroup)->push(...$page::navigationItems());
                } else {
                    $this->items->push(...$page::navigationItems());
                }
            }
        });

        // Then we push those groups into the navigation based on the nav menus name.
        $this->items->push(...NavigationGroup::menuGroups($this->navName));

        $this->items = $this->items->sortBy(fn ($item) => $item->sort)->values();
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
