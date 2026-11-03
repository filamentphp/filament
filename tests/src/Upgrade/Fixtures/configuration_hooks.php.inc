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

class ChildLabels extends Labels
{
    protected static ?string $label = 'Customer';
}

class LabelGetters extends Resource
{
    public static ?string $value = 'Mouse';

    public static int $labelCalls = 0;

    public static function getLabel(): ?string
    {
        static::$labelCalls++;

        return static::$value;
    }

    public static function getPluralLabel(): ?string
    {
        return '';
    }
}

class ChildLabelGetters extends LabelGetters
{
    public static ?string $value = null;

    public static function getLabel(): ?string
    {
        return parent::getLabel() ?? 'Field mouse';
    }

    public static function getPluralLabel(): ?string
    {
        return parent::getPluralLabel() ?? 'Rodents';
    }
}

class Stats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Revenue', 31)];
    }
}

class ChildStats extends Stats
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Expenses', 17)];
    }
}

class Actions extends Page
{
    protected function getActions(): array
    {
        return [...parent::getActions(), Action::make('export')->action(fn () => null)];
    }
}

class IntermediateActions extends Actions {}

class ChildActions extends IntermediateActions
{
    protected function getActions(): array
    {
        return [...parent::getActions(), Action::make('print')->action(fn () => null)];
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

class ChildCreate extends Create
{
    protected function getCreatedNotificationMessage(): ?string
    {
        return parent::getCreatedNotificationMessage() ?? 'Child created';
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

class ChildEdit extends Edit
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() ?? 'Child saved';
    }
}

class Settings extends SettingsPage
{
    protected function getSavedNotificationMessage(): ?string
    {
        return 'Settings saved';
    }
}

class ChildSettings extends Settings
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() . ' again';
    }
}

class IntermediateSettings extends SettingsPage {}

class IndirectSettings extends IntermediateSettings
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() ?? 'Indirect settings saved';
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

class ChildFilters extends Filters
{
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

class ChildProfile extends Profile
{
    public function backAction(): Action
    {
        return parent::backAction()->label('Child');
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

class ChildLabels extends Labels
{
    protected static ?string $modelLabel = 'Customer';
}

class LabelGetters extends Resource
{
    public static ?string $value = 'Mouse';

    public static int $labelCalls = 0;

    public static function getModelLabel(): string
    {
        return static::getLabel() ?? parent::getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return \filled($modelLabel = static::getPluralLabel()) ? $modelLabel : parent::getPluralModelLabel();
    }
    public static function getLabel(): ?string
    {
        static::$labelCalls++;

        return static::$value;
    }
    public static function getPluralLabel(): ?string
    {
        return '';
    }
}

class ChildLabelGetters extends LabelGetters
{
    public static ?string $value = null;

    public static function getLabel(): ?string
    {
        return parent::getLabel() ?? 'Field mouse';
    }

    public static function getPluralLabel(): ?string
    {
        return parent::getPluralLabel() ?? 'Rodents';
    }
}

class Stats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [...[], Stat::make('Revenue', 31)];
    }
}

class ChildStats extends Stats
{
    protected function getStats(): array
    {
        return [...parent::getStats(), Stat::make('Expenses', 17)];
    }
}

class Actions extends Page
{
    protected function getHeaderActions(): array
    {
        return [...[], Action::make('export')->action(fn () => null)];
    }
}

class IntermediateActions extends Actions {}

class ChildActions extends IntermediateActions
{
    protected function getHeaderActions(): array
    {
        return [...parent::getHeaderActions(), Action::make('print')->action(fn () => null)];
    }
}

class Create extends CreateRecord
{
    public ?string $message = null;

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->getCreatedNotificationMessage() ?? \__('filament-panels::resources/pages/create-record.notifications.created.title');
    }
    protected function getCreatedNotificationMessage(): ?string
    {
        return $this->message;
    }
}

class ChildCreate extends Create
{
    protected function getCreatedNotificationMessage(): ?string
    {
        return parent::getCreatedNotificationMessage() ?? 'Child created';
    }
}

class Edit extends EditRecord
{
    protected function getSavedNotificationTitle(): ?string
    {
        return $this->getSavedNotificationMessage() ?? \__('filament-panels::resources/pages/edit-record.notifications.saved.title');
    }
    protected function getSavedNotificationMessage(): ?string
    {
        if ($this->record) {
            return '';
        }

        return null;
    }
}

class ChildEdit extends Edit
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() ?? 'Child saved';
    }
}

class Settings extends SettingsPage
{
    #[Unexposed]
    public function getSavedNotificationTitle(): ?string
    {
        return $this->getSavedNotificationMessage() ?? \__('filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title');
    }
    protected function getSavedNotificationMessage(): ?string
    {
        return 'Settings saved';
    }
}

class ChildSettings extends Settings
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() . ' again';
    }
}

class IntermediateSettings extends SettingsPage {}

class IndirectSettings extends IntermediateSettings
{
    #[Unexposed]
    public function getSavedNotificationTitle(): ?string
    {
        return (function (): ?string {
            return null ?? 'Indirect settings saved';
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

class ChildFilters extends Filters
{
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

class ChildProfile extends Profile
{
    #[Unexposed]
    public function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Child');
    }
}
