<?php

namespace Filament\JetstreamExtension\Http\Livewire;

use Filament\Services\NavigationService;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Class NavigationMenu
 * @package Filament\JetstreamExtension\Http\Livewire
 */
class NavigationMenu extends Component
{
    /**
     * @var \Illuminate\Support\Collection
     */
    public Collection $items;

    /**
     * The component's listeners.
     *
     * @var array
     */
    protected $listeners = [
        'refresh-navigation-menu' => '$refresh',
    ];

    /**
     * Nav constructor.
     *
     * @param  NavigationService  $navigationService
     */
    public function mount(NavigationService $navigationService)
    {
        $this->items = $navigationService
            ->withDashboard()
            ->withResources()
            ->withPages()
            ->getItems();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('jetstream-extension::components.navigation-menu');
    }
}
