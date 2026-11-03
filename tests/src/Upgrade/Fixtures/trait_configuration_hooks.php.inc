<?php

namespace Filament\Tests\Upgrade\Fixtures\TraitConfigurationHooks;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

trait HasActions
{
    public int $actionCalls = 0;

    protected function getActions(): array
    {
        $this->actionCalls++;

        return [...parent::getActions(), Action::make('shared')->action(fn () => null)];
    }
}

trait HasNestedActions
{
    use HasActions;
}

class Actions extends Page
{
    use HasNestedActions;
}

trait HasChildActions
{
    protected function getActions(): array
    {
        return [...parent::getActions(), Action::make('child')->action(fn () => null)];
    }
}

class ChildActions extends Actions
{
    use HasChildActions;
}

class PlainParent
{
    protected function getActions(): array { return ['unrelated']; }
}

class PlainConsumer extends PlainParent
{
    use HasActions;

    public function actions(): array { return $this->getActions(); }
}

trait HasLabels
{
    public static ?string $value = 'Mouse';

    public static int $labelCalls = 0;

    public static function getLabel(): ?string
    {
        static::$labelCalls++;

        return parent::getLabel() ?? static::$value;
    }

    public static function getPluralLabel(): ?string
    {
        return parent::getPluralLabel();
    }
}

class Labels extends Resource
{
    use HasLabels;
}

trait HasChildLabel
{
    public static function getLabel(): ?string
    {
        return parent::getLabel() ?? 'Field mouse';
    }
}

class ChildLabels extends Labels
{
    use HasChildLabel;

    public static ?string $value = null;
}

trait HasCreatedMessage
{
    protected function getCreatedNotificationMessage(): ?string
    {
        return parent::getCreatedNotificationMessage() ?? $this->message;
    }
}

class Create extends CreateRecord
{
    use HasCreatedMessage;

    public ?string $message = null;
}

class ChildCreate extends Create
{
    protected function getCreatedNotificationMessage(): ?string
    {
        return parent::getCreatedNotificationMessage() ?? 'Trait child created';
    }
}

trait HasSavedMessage
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() ?? $this->message;
    }
}

class Edit extends EditRecord
{
    use HasSavedMessage;

    public ?string $message = null;
}

class Settings extends SettingsPage
{
    use HasSavedMessage;

    public ?string $message = null;
}

trait HasCards
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Revenue', 43)];
    }
}

class Stats extends StatsOverviewWidget
{
    use HasCards;
}

class ChildStats extends Stats
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Expenses', 19)];
    }
}

trait HasBackAction
{
    public int $backActionCalls = 0;

    public function backAction(): Action
    {
        $this->backActionCalls++;

        return parent::backAction()->label('Trait return');
    }
}

class Profile extends EditProfile
{
    use HasBackAction;
}

class ChildProfile extends Profile
{
    public function backAction(): Action
    {
        $action = parent::backAction();

        return $action->label($action->getLabel() . ' child');
    }
}

class LegacyTraitPage extends Page
{
    protected function getActions(): array { return [Action::make('base')->action(fn () => null)]; }
}

class ChildTraitPage extends LegacyTraitPage
{
    use HasChildActions;
}

trait HasLabelConfiguration
{
    protected static ?string $label = 'Customer';

    protected static ?string $pluralLabel = '';
}

class PropertyLabelParent extends Resource
{
    protected static ?string $label = 'Customer';

    protected static ?string $pluralLabel = '';
}

class PropertyLabels extends PropertyLabelParent
{
    use HasLabelConfiguration;
}

class ChildPropertyLabels extends PropertyLabels
{
    protected static ?string $label = 'Partner';
}

trait UnrelatedBehavior {}

class IntermediateResource extends Resource {}

class DeclaredPropertyLabels extends IntermediateResource
{
    protected static ?string $label = 'Parent';
}

class ChildDeclaredPropertyLabels extends DeclaredPropertyLabels
{
    use UnrelatedBehavior;

    protected static ?string $label = 'Child';
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\TraitConfigurationHooks;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

trait HasActions
{
    public int $actionCalls = 0;

    protected function getActions(): array
    {
        $this->actionCalls++;

        return [...parent::getActions(), Action::make('shared')->action(fn () => null)];
    }
}

trait HasNestedActions
{
    use HasActions;
}

class Actions extends Page
{
    use HasNestedActions;
    protected function getActions(): array
    {
        $this->actionCalls++;
        return [...[], Action::make('shared')->action(fn() => null)];
    }
    protected function getHeaderActions(): array
    {
        return $this->getActions();
    }
}

trait HasChildActions
{
    protected function getActions(): array
    {
        return [...parent::getActions(), Action::make('child')->action(fn () => null)];
    }
}

class ChildActions extends Actions
{
    use HasChildActions;
}

class PlainParent
{
    protected function getActions(): array { return ['unrelated']; }
}

class PlainConsumer extends PlainParent
{
    use HasActions;

    public function actions(): array { return $this->getActions(); }
}

trait HasLabels
{
    public static ?string $value = 'Mouse';

    public static int $labelCalls = 0;

    public static function getLabel(): ?string
    {
        static::$labelCalls++;

        return parent::getLabel() ?? static::$value;
    }

    public static function getPluralLabel(): ?string
    {
        return parent::getPluralLabel();
    }
}

class Labels extends Resource
{
    use HasLabels;
    public static function getLabel(): ?string
    {
        static::$labelCalls++;
        return null ?? static::$value;
    }
    public static function getModelLabel(): string
    {
        return static::getLabel() ?? parent::getModelLabel();
    }
    public static function getPluralLabel(): ?string
    {
        return null;
    }
    public static function getPluralModelLabel(): string
    {
        return \filled($modelLabel = static::getPluralLabel()) ? $modelLabel : parent::getPluralModelLabel();
    }
}

trait HasChildLabel
{
    public static function getLabel(): ?string
    {
        return parent::getLabel() ?? 'Field mouse';
    }
}

class ChildLabels extends Labels
{
    use HasChildLabel;

    public static ?string $value = null;
}

trait HasCreatedMessage
{
    protected function getCreatedNotificationMessage(): ?string
    {
        return parent::getCreatedNotificationMessage() ?? $this->message;
    }
}

class Create extends CreateRecord
{
    use HasCreatedMessage;

    public ?string $message = null;
    protected function getCreatedNotificationMessage(): ?string
    {
        return null ?? $this->message;
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->getCreatedNotificationMessage() ?? \__('filament-panels::resources/pages/create-record.notifications.created.title');
    }
}

class ChildCreate extends Create
{
    protected function getCreatedNotificationMessage(): ?string
    {
        return parent::getCreatedNotificationMessage() ?? 'Trait child created';
    }
}

trait HasSavedMessage
{
    protected function getSavedNotificationMessage(): ?string
    {
        return parent::getSavedNotificationMessage() ?? $this->message;
    }
}

class Edit extends EditRecord
{
    use HasSavedMessage;

    public ?string $message = null;
    protected function getSavedNotificationMessage(): ?string
    {
        return null ?? $this->message;
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return $this->getSavedNotificationMessage() ?? \__('filament-panels::resources/pages/edit-record.notifications.saved.title');
    }
}

class Settings extends SettingsPage
{
    use HasSavedMessage;

    public ?string $message = null;
    protected function getSavedNotificationMessage(): ?string
    {
        return null ?? $this->message;
    }
    #[Unexposed]
    public function getSavedNotificationTitle(): ?string
    {
        return $this->getSavedNotificationMessage() ?? \__('filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title');
    }
}

trait HasCards
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Revenue', 43)];
    }
}

class Stats extends StatsOverviewWidget
{
    use HasCards;
    protected function getCards(): array
    {
        return [...[], Stat::make('Revenue', 43)];
    }
    protected function getStats(): array
    {
        return $this->getCards();
    }
}

class ChildStats extends Stats
{
    protected function getCards(): array
    {
        return [...parent::getCards(), Stat::make('Expenses', 19)];
    }
}

trait HasBackAction
{
    public int $backActionCalls = 0;

    public function backAction(): Action
    {
        $this->backActionCalls++;

        return parent::backAction()->label('Trait return');
    }
}

class Profile extends EditProfile
{
    use HasBackAction;
    public function backAction(): Action
    {
        $this->backActionCalls++;
        return parent::getCancelFormAction()->label('Trait return');
    }
    #[Unexposed]
    public function getCancelFormAction(): Action
    {
        return $this->backAction();
    }
}

class ChildProfile extends Profile
{
    public function backAction(): Action
    {
        $action = parent::backAction();

        return $action->label($action->getLabel() . ' child');
    }
}

class LegacyTraitPage extends Page
{
    protected function getHeaderActions(): array { return $this->getActions(); }
    protected function getActions(): array { return [Action::make('base')->action(fn () => null)]; }
}

class ChildTraitPage extends LegacyTraitPage
{
    use HasChildActions;
}

trait HasLabelConfiguration
{
    protected static ?string $label = 'Customer';

    protected static ?string $pluralLabel = '';
}

class PropertyLabelParent extends Resource
{
    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = '';
}

class PropertyLabels extends PropertyLabelParent
{
    use HasLabelConfiguration;
    protected static ?string $modelLabel = 'Customer';
    protected static ?string $pluralModelLabel = '';
}

class ChildPropertyLabels extends PropertyLabels
{
    protected static ?string $modelLabel = 'Partner';
}

trait UnrelatedBehavior {}

class IntermediateResource extends Resource {}

class DeclaredPropertyLabels extends IntermediateResource
{
    protected static ?string $modelLabel = 'Parent';
}

class ChildDeclaredPropertyLabels extends DeclaredPropertyLabels
{
    use UnrelatedBehavior;

    protected static ?string $modelLabel = 'Child';
}
