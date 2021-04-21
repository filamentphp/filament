<?php

namespace Filament\View\Components;

use Filament\Filament;
use Filament\View\NavigationGroup;
use Filament\View\NavigationItem;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Nav extends Component
{
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

        foreach (Filament::getResources() as $resource) {
            if (Filament::can('viewAny', $resource::getModel())) {
                if ($resource::hasNavigationGroup()) {
                    NavigationGroup::group($resource::$navigationGroup)->push(...$resource::navigationItems());
                } else {
                    $this->items->push(...$resource::navigationItems());
                }
            }
        }

        foreach (Filament::getPages() as $page) {
            if (Filament::can('view', $page)) {
                $this->items->push(...$page::navigationItems());
            }
        }

        $this->items->push(...NavigationGroup::groups());

        $this->items = $this->items->sortBy(fn ($item) => $item->sort)->values();
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
