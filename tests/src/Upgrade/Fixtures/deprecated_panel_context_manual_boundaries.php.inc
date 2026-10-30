<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelContextManualBoundaries;

use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Panel;
use Filament\Panel\Concerns\HasDarkMode;

class LegacyManager extends FilamentManager
{
    public function currentDomain(?string $domain): void
    {
        parent::currentDomain($domain);
    }

    public function hasDarkModeForced(): bool
    {
        return parent::hasDarkModeForced();
    }
}

class CanonicalManager extends FilamentManager
{
    public function setCurrentDomain(?string $domain): void
    {
        parent::setCurrentDomain($domain);
    }

    public function isDarkModeForced(): bool
    {
        return parent::isDarkModeForced();
    }
}

trait CustomForcedState
{
    public function hasDarkModeForced(): bool
    {
        return true;
    }
}

class LegacyPanel extends Panel
{
    use CustomForcedState;
}

class CanonicalPanel extends Panel
{
    public function isDarkModeForced(): bool
    {
        return parent::isDarkModeForced();
    }
}

class DirectDarkMode
{
    use HasDarkMode;
}

class CustomFacade extends Filament
{
    public static function configure(): void
    {
        self::currentDomain(null);
        static::currentDomain(null);
        parent::currentDomain(null);
    }
}

function manual(LegacyManager $legacyManager, CanonicalManager $canonicalManager, LegacyPanel $legacyPanel, CanonicalPanel $canonicalPanel, DirectDarkMode $direct, FilamentManager $manager, object $unrelated, mixed $unknown, string $method, string $facadeClass): void
{
    $legacyManager->currentDomain(null);
    $legacyManager->hasDarkModeForced();
    $canonicalManager->currentDomain(null);
    $canonicalManager->hasDarkModeForced();
    $legacyPanel->hasDarkModeForced();
    $canonicalPanel->hasDarkModeForced();
    $direct->hasDarkModeForced();
    $unrelated->currentDomain(null);
    $unrelated->hasDarkModeForced();
    $unknown->hasDarkModeForced();
    $manager->{$method}(null);
    $manager->{'currentDomain'}(null);
    Filament::{$method}(null);
    $facadeClass::currentDomain(null);
    CustomFacade::currentDomain(null);
    CustomFacade::hasDarkModeForced();
    FilamentManager::currentDomain(null);
    Panel::hasDarkModeForced();
    filament('plugin')->hasDarkModeForced();
    $callback = [$manager, 'currentDomain'];
    $callback = [Filament::class, 'hasDarkModeForced'];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelContextManualBoundaries;

use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Panel;
use Filament\Panel\Concerns\HasDarkMode;

class LegacyManager extends FilamentManager
{
    public function currentDomain(?string $domain): void
    {
        parent::setCurrentDomain($domain);
    }

    public function hasDarkModeForced(): bool
    {
        return parent::isDarkModeForced();
    }
}

class CanonicalManager extends FilamentManager
{
    public function setCurrentDomain(?string $domain): void
    {
        parent::setCurrentDomain($domain);
    }

    public function isDarkModeForced(): bool
    {
        return parent::isDarkModeForced();
    }
}

trait CustomForcedState
{
    public function hasDarkModeForced(): bool
    {
        return true;
    }
}

class LegacyPanel extends Panel
{
    use CustomForcedState;
}

class CanonicalPanel extends Panel
{
    public function isDarkModeForced(): bool
    {
        return parent::isDarkModeForced();
    }
}

class DirectDarkMode
{
    use HasDarkMode;
}

class CustomFacade extends Filament
{
    public static function configure(): void
    {
        self::currentDomain(null);
        static::currentDomain(null);
        parent::currentDomain(null);
    }
}

function manual(LegacyManager $legacyManager, CanonicalManager $canonicalManager, LegacyPanel $legacyPanel, CanonicalPanel $canonicalPanel, DirectDarkMode $direct, FilamentManager $manager, object $unrelated, mixed $unknown, string $method, string $facadeClass): void
{
    $legacyManager->currentDomain(null);
    $legacyManager->hasDarkModeForced();
    $canonicalManager->currentDomain(null);
    $canonicalManager->hasDarkModeForced();
    $legacyPanel->hasDarkModeForced();
    $canonicalPanel->hasDarkModeForced();
    $direct->hasDarkModeForced();
    $unrelated->currentDomain(null);
    $unrelated->hasDarkModeForced();
    $unknown->hasDarkModeForced();
    $manager->{$method}(null);
    $manager->{'currentDomain'}(null);
    Filament::{$method}(null);
    $facadeClass::currentDomain(null);
    CustomFacade::currentDomain(null);
    CustomFacade::hasDarkModeForced();
    FilamentManager::currentDomain(null);
    Panel::hasDarkModeForced();
    filament('plugin')->hasDarkModeForced();
    $callback = [$manager, 'currentDomain'];
    $callback = [Filament::class, 'hasDarkModeForced'];
}
