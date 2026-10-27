<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooks;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class Labels extends Resource
{
    protected static ?string $label = 'Person';

    protected static ?string $pluralLabel = '';
}

class LabelGetters extends Resource
{
    public static function getLabel(): ?string
    {
        return 'Mouse';
    }

    public static function getPluralLabel(): ?string
    {
        return '';
    }
}

class Stats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Revenue', 31)];
    }
}

class Actions extends Page
{
    protected function getActions(): array
    {
        return [...parent::getActions(), Action::make('export')->action(fn () => null)];
    }
}

class Create extends CreateRecord
{
    public ?string $message = null;

    protected function getCreatedNotificationMessage(): ?string
    {
        return $this->message;
    }
}

class Edit extends EditRecord
{
    protected function getSavedNotificationMessage(): ?string
    {
        if ($this->record) {
            return '';
        }

        return parent::getSavedNotificationMessage();
    }
}

class Settings extends SettingsPage
{
    protected function getSavedNotificationMessage(): ?string
    {
        return 'Settings saved';
    }
}

class Filters extends Widget
{
    use InteractsWithPageFilters;

    public function selectedPeriod(): ?string
    {
        return $this->filters['period'] ?? null;
    }
}

class Profile extends EditProfile
{
    public function backAction(): Action
    {
        return parent::backAction()->label('Return');
    }

    public static function configureAlignment(): void
    {
        self::alignFormActionsLeft();
        static::alignFormActionsRight();
        parent::alignFormActionsLeft();
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooks;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class Labels extends Resource
{
    protected static ?string $modelLabel = 'Person';

    protected static ?string $pluralModelLabel = '';
}

class LabelGetters extends Resource
{
    public static function getModelLabel(): string
    {
        return (static function (): ?string {
            return 'Mouse';
        })() ?? parent::getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return \filled($modelLabel = (static function (): ?string {
            return '';
        })()) ? $modelLabel : parent::getPluralModelLabel();
    }
}

class Stats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [...[], Stat::make('Revenue', 31)];
    }
}

class Actions extends Page
{
    protected function getHeaderActions(): array
    {
        return [...[], Action::make('export')->action(fn () => null)];
    }
}

class Create extends CreateRecord
{
    public ?string $message = null;

    protected function getCreatedNotificationTitle(): ?string
    {
        return (function (): ?string {
            return $this->message;
        })() ?? \__('filament-panels::resources/pages/create-record.notifications.created.title');
    }
}

class Edit extends EditRecord
{
    protected function getSavedNotificationTitle(): ?string
    {
        return (function (): ?string {
            if ($this->record) {
                return '';
            }
            return null;
        })() ?? \__('filament-panels::resources/pages/edit-record.notifications.saved.title');
    }
}

class Settings extends SettingsPage
{
    #[Unexposed]
    public function getSavedNotificationTitle(): ?string
    {
        return (function (): ?string {
            return 'Settings saved';
        })() ?? \__('filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title');
    }
}

class Filters extends Widget
{
    use InteractsWithPageFilters;

    public function selectedPeriod(): ?string
    {
        return $this->pageFilters['period'] ?? null;
    }
}

class Profile extends EditProfile
{
    #[Unexposed]
    public function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Return');
    }

    public static function configureAlignment(): void
    {
        self::alignFormActionsStart();
        static::alignFormActionsEnd();
        parent::alignFormActionsStart();
    }
}
