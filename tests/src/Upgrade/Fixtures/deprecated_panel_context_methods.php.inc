<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelContextMethods;

use Filament\Facades\Filament;
use Filament\Facades\Filament as PanelFacade;
use Filament\FilamentManager;
use Filament\Panel;

use function filament as manager;

class CustomManager extends FilamentManager {}

class CustomPanel extends Panel {}

function configure(FilamentManager $manager, ?FilamentManager $nullableManager, CustomManager $customManager, Panel $panel, ?Panel $nullablePanel, CustomPanel $customPanel, array $arguments): void
{
    $manager->currentDomain(domain: 'admin.example.com');
    $manager->hasDarkModeForced();
    $nullableManager?->currentDomain(null);
    $nullableManager?->hasDarkModeForced();
    $customManager->currentDomain(domain: null);
    $customManager->hasDarkModeForced();
    $panel->hasDarkModeForced();
    $nullablePanel?->hasDarkModeForced();
    $customPanel->darkMode(isForced: true)->hasDarkModeForced();
    Panel::make()->hasDarkModeForced();
    filament()->currentDomain(domain: null);
    filament()->hasDarkModeForced();
    manager()->hasDarkModeForced();
    Filament::currentDomain(domain: 'admin.example.com');
    PanelFacade::hasDarkModeForced();
    \Filament\Facades\Filament::currentDomain(null);
    Filament::HASDARKMODEFORCED();
    $manager->currentDomain(...$arguments);
    Filament::currentDomain(...$arguments);
    $callback = $manager->currentDomain(...);
    $callback = $panel->hasDarkModeForced(...);
    $callback = Filament::currentDomain(...);
    $callback = Filament::hasDarkModeForced(...);
    $manager->setCurrentDomain(domain: null);
    $manager->isDarkModeForced();
    $panel->isDarkModeForced();
    Filament::setCurrentDomain(domain: null);
    Filament::isDarkModeForced();
    Filament::hasDarkMode();
    $panel->hasDarkMode();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelContextMethods;

use Filament\Facades\Filament;
use Filament\Facades\Filament as PanelFacade;
use Filament\FilamentManager;
use Filament\Panel;

use function filament as manager;

class CustomManager extends FilamentManager {}

class CustomPanel extends Panel {}

function configure(FilamentManager $manager, ?FilamentManager $nullableManager, CustomManager $customManager, Panel $panel, ?Panel $nullablePanel, CustomPanel $customPanel, array $arguments): void
{
    $manager->setCurrentDomain(domain: 'admin.example.com');
    $manager->isDarkModeForced();
    $nullableManager?->setCurrentDomain(null);
    $nullableManager?->isDarkModeForced();
    $customManager->setCurrentDomain(domain: null);
    $customManager->isDarkModeForced();
    $panel->isDarkModeForced();
    $nullablePanel?->isDarkModeForced();
    $customPanel->darkMode(isForced: true)->isDarkModeForced();
    Panel::make()->isDarkModeForced();
    filament()->setCurrentDomain(domain: null);
    filament()->isDarkModeForced();
    manager()->isDarkModeForced();
    PanelFacade::setCurrentDomain(domain: 'admin.example.com');
    PanelFacade::isDarkModeForced();
    PanelFacade::setCurrentDomain(null);
    PanelFacade::isDarkModeForced();
    $manager->setCurrentDomain(...$arguments);
    PanelFacade::setCurrentDomain(...$arguments);
    $callback = $manager->setCurrentDomain(...);
    $callback = $panel->isDarkModeForced(...);
    $callback = PanelFacade::setCurrentDomain(...);
    $callback = PanelFacade::isDarkModeForced(...);
    $manager->setCurrentDomain(domain: null);
    $manager->isDarkModeForced();
    $panel->isDarkModeForced();
    PanelFacade::setCurrentDomain(domain: null);
    PanelFacade::isDarkModeForced();
    PanelFacade::hasDarkMode();
    $panel->hasDarkMode();
}
