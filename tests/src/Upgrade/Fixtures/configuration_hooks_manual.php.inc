<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooksManual;

use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Widget;

class ParentStats extends StatsOverviewWidget
{
    protected function getCards(): array { return ['old']; }
}

class ChildStats extends ParentStats
{
    protected function getStats(): array { return ['modern', ...parent::getStats()]; }
}

class BothNames extends Page
{
    protected function getActions(): array { return []; }
    protected function getHeaderActions(): array { return []; }
}

class BothLabels extends Resource
{
    protected static ?string $label = 'old';
    protected static ?string $modelLabel = '';
    protected static ?string $pluralLabel = 'older';
    protected static ?string $pluralModelLabel = '';
}

class ReferencedLabel extends Resource
{
    protected static ?string $label = 'old';
    public static function read(): ?string { return static::$label; }
}

class FinallyNotification extends EditRecord
{
    protected function getSavedNotificationMessage(): ?string
    {
        try {
            return null;
        } finally {
            app()->setLocale('fr');
        }
    }
}

class WrittenFilters extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        $this->filters['period'] = 'custom';

        return $this->filters;
    }
}

class ReferencedFilters extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        mutate($this->filters);

        return $this->filters;
    }
}

class RealFilters extends Widget
{
    use InteractsWithPageFilters;

    public array $filters = [];

    public function report(): array { return $this->filters; }
}

class ExternallyCalledResource extends Resource
{
    public static function getLabel(): ?string { return 'Product'; }
}

function label(): ?string
{
    return ExternallyCalledResource::getLabel();
}

class AlignmentParent extends Page
{
    public static function configureAlignment(): void
    {
        parent::alignFormActionsLeft();
        self::alignFormActionsRight();
        static::alignFormActionsLeft();
    }
}

class ModernAlignment extends AlignmentParent
{
    public static function alignFormActionsStart(): void {}
}

class LegacyAlignment extends AlignmentParent
{
    public static function alignFormActionsLeft(): void {}
}

class PublicLabel extends Resource
{
    public static ?string $label = 'Visible';
}

function publicLabel(): ?string
{
    return PublicLabel::$label;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooksManual;

use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Widget;

class ParentStats extends StatsOverviewWidget
{
    protected function getCards(): array { return ['old']; }
}

class ChildStats extends ParentStats
{
    protected function getStats(): array { return ['modern', ...parent::getStats()]; }
}

class BothNames extends Page
{
    protected function getActions(): array { return []; }
    protected function getHeaderActions(): array { return []; }
}

class BothLabels extends Resource
{
    protected static ?string $label = 'old';
    protected static ?string $modelLabel = '';
    protected static ?string $pluralLabel = 'older';
    protected static ?string $pluralModelLabel = '';
}

class ReferencedLabel extends Resource
{
    protected static ?string $label = 'old';
    public static function read(): ?string { return static::$label; }
}

class FinallyNotification extends EditRecord
{
    protected function getSavedNotificationTitle(): ?string
    {
        return (function (): ?string {
            try {
                return null;
            } finally {
                app()->setLocale('fr');
            }
        })() ?? \__('filament-panels::resources/pages/edit-record.notifications.saved.title');
    }
}

class WrittenFilters extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        $this->filters['period'] = 'custom';

        return $this->filters;
    }
}

class ReferencedFilters extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        mutate($this->filters);

        return $this->filters;
    }
}

class RealFilters extends Widget
{
    use InteractsWithPageFilters;

    public array $filters = [];

    public function report(): array { return $this->filters; }
}

class ExternallyCalledResource extends Resource
{
    public static function getLabel(): ?string { return 'Product'; }
}

function label(): ?string
{
    return ExternallyCalledResource::getLabel();
}

class AlignmentParent extends Page
{
    public static function configureAlignment(): void
    {
        parent::alignFormActionsLeft();
        self::alignFormActionsRight();
        static::alignFormActionsLeft();
    }
}

class ModernAlignment extends AlignmentParent
{
    public static function alignFormActionsStart(): void {}
}

class LegacyAlignment extends AlignmentParent
{
    public static function alignFormActionsLeft(): void {}
}

class PublicLabel extends Resource
{
    public static ?string $label = 'Visible';
}

function publicLabel(): ?string
{
    return PublicLabel::$label;
}
