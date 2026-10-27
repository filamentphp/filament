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

use Filament\Exceptions\NoDefaultPanelSetException;
use LogicException;
use Illuminate\Contracts\Support\Htmlable;
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
    (static function (array $pages): void {
        try {
            Filament::getDefaultPanel()->pages($pages);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `pages()` method on the panel configuration to register pages.');
        }
    })([Page::class]);
    (static function (array $resources): void {
        try {
            Filament::getDefaultPanel()->resources($resources);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `resources()` method on the panel configuration to register resources.');
        }
    })([Resource::class]);
    (static function (array $widgets): void {
        try {
            Filament::getDefaultPanel()->widgets($widgets);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `widgets()` method on the panel configuration to register widgets.');
        }
    })([StatsOverviewWidget::class]);
    (static function (string|Htmlable|null $theme): void {
        try {
            Filament::getDefaultPanel()->theme($theme);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `theme()` method on the panel configuration to register themes.');
        }
    })('theme.css');
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
