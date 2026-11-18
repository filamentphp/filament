<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelIntegrationsManual;

use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\StatsOverviewWidget;

function manual(FilamentManager $manager, string $method, array $arguments): void
{
    $manager->registerScriptData(['custom' => true]);
    Filament::$method(...$arguments);
    Filament::registerRenderHook(...$arguments);
    Filament::registerScriptData(['ignored' => true], 'ignored-package');
    Filament::renderHook('hook', 'ignored-scope');
    Filament::registerRenderHook('hook', fn () => 'html', 'ignored-scope');
    Filament::registerScriptData(...);
    Filament::registerPages([Page::class]);
    Filament::registerResources([Resource::class]);
    Filament::registerWidgets([StatsOverviewWidget::class]);
    Filament::registerTheme('theme.css');
    Filament::registerScripts(['script.js']);
    Filament::registerStyles(['style.css']);
}

trait LegacyStats
{
    protected function getCards(): array
    {
        return [];
    }
}

class ParentStats extends StatsOverviewWidget
{
    use LegacyStats;

    protected function getStats(): array
    {
        return [...parent::getStats(), ...$this->getCards()];
    }
}

class ChildStats extends ParentStats
{
    protected function getCards(): array
    {
        return [...parent::getCards()];
    }
}

class Labels extends Resource
{
    protected static ?string $label = 'old';

    protected static ?string $modelLabel = 'modern';

    public static function getLabel(): ?string
    {
        return parent::getLabel();
    }
}

class ActionsPage extends Page
{
    protected function getActions(): array
    {
        return parent::getActions();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelIntegrationsManual;

use Filament\Support\Facades\FilamentAsset;
use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\StatsOverviewWidget;

function manual(FilamentManager $manager, string $method, array $arguments): void
{
    FilamentAsset::registerScriptData(['custom' => true]);
    Filament::$method(...$arguments);
    Filament::registerRenderHook(...$arguments);
    Filament::registerScriptData(['ignored' => true], 'ignored-package');
    Filament::renderHook('hook', 'ignored-scope');
    Filament::registerRenderHook('hook', fn () => 'html', 'ignored-scope');
    Filament::registerScriptData(...);
    Filament::getDefaultPanel()->pages([Page::class]);
    Filament::getDefaultPanel()->resources([Resource::class]);
    Filament::getDefaultPanel()->widgets([StatsOverviewWidget::class]);
    Filament::getDefaultPanel()->theme('theme.css');
    Filament::registerScripts(['script.js']);
    Filament::registerStyles(['style.css']);
}

trait LegacyStats
{
    protected function getCards(): array
    {
        return [];
    }
}

class ParentStats extends StatsOverviewWidget
{
    use LegacyStats;

    protected function getStats(): array
    {
        return [...parent::getStats(), ...$this->getCards()];
    }
}

class ChildStats extends ParentStats
{
    protected function getCards(): array
    {
        return [...parent::getCards()];
    }
}

class Labels extends Resource
{
    protected static ?string $label = 'old';

    protected static ?string $modelLabel = 'modern';

    public static function getLabel(): ?string
    {
        return parent::getLabel();
    }
}

class ActionsPage extends Page
{
    protected function getActions(): array
    {
        return parent::getActions();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
