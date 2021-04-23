<?php

namespace Filament\View\Components;

use Filament\Filament;
use Filament\Resources\Concerns\IsGroupResource;
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

        $appResources = collect(Filament::getResources());
        $resourcesAsGroups = $appResources->lazy()->filter(fn($resource) => (new $resource) instanceof IsGroupResource);
        $resourcesAsGroups->each(static function ($resource) {
            if (Filament::can('viewAny', $resource::getModel())) {
                $resource::registerNavigationGroup();
            }
        });

        $regularResources = $appResources->lazy()->filter(fn($resource) => !((new $resource) instanceof IsGroupResource));
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

        foreach (Filament::getPages() as $page) {
            // TODO: update the pages to allow for groupings too.
            if (Filament::can('view', $page)) {
                $this->items->push(...$page::navigationItems());
            }
        }

        // Then we push those groups into the navigation based on the nav menus name.
        $this->items->push(...NavigationGroup::menuGroups($this->navName));

        $this->items = $this->items->sortBy(fn ($item) => $item->sort)->values();
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
