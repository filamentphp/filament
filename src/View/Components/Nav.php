<?php

namespace Filament\View\Components;

use Filament\Services\NavigationService;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * Class Nav
 * @package Filament\View\Components
 */
class Nav extends Component
{
    /**
     * @var \Illuminate\Support\Collection
     */
    public Collection $items;

    /**
     * Nav constructor.
     *
     * @param  NavigationService  $navigationService
     */
    public function __construct(NavigationService $navigationService)
    {
        $this->items = $navigationService->getItems(true);
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('filament::components.nav');
    }
}
