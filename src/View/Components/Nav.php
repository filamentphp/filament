<?php

namespace Filament\View\Components;

use Filament\Filament;
use Filament\NavigationItem;
use Illuminate\View\Component;

class Nav extends Component
{
    public $items;

    public function __construct()
    {
        $this->items = collect();

        $this->items->push(
            NavigationItem::make('filament::dashboard.title', route('filament.dashboard'))
                ->activeRule('filament.dashboard')
                ->icon('heroicon-o-home')
                ->sort(-1),
        );

        foreach (Filament::getResources() as $resource) {
            if (Filament::can('viewAny', $resource::getModel())) {
                $this->items->push(...$resource::navigationItems());
            }
        }

        foreach (Filament::getPages() as $page) {
            if (Filament::can('view', $page)) {
                $this->items->push(...$page::navigationItems());
            }
        }

        $this->items = $this->items->sortBy(fn ($item) => $item->sort)->values();
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
