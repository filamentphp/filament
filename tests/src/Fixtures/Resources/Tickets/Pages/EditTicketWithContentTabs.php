<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Enums\ContentTabPosition;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\ContentTabsDepartmentsRelationManager;

class EditTicketWithContentTabs extends EditTicket
{
    public bool $hasManagers = true;

    public bool $hasGroupedManagers = false;

    public bool $hasCustomTabs = true;

    public bool $isContentAfter = false;

    public bool $isPreferencesHidden = false;

    public bool $isPreferencesRemoved = false;

    public string $activeLocale = 'fr';

    public ?array $preferencesData = [];

    public string $savedPreference = '';

    public bool $isEditSaved = false;

    public function mount(int | string $record): void
    {
        $this->hasManagers = ! request()->boolean('no-managers');
        $this->hasGroupedManagers = request()->boolean('grouped');
        $this->hasCustomTabs = ! request()->boolean('default-tabs');
        $this->isContentAfter = request()->boolean('after');
        $this->isPreferencesHidden = request()->boolean('hidden');
        $this->isPreferencesRemoved = request()->boolean('removed');

        parent::mount($record);

        $this->preferencesForm->fill();
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabComponent(): Tab
    {
        return parent::getContentTabComponent()->label('Edit ticket')->id('edit-ticket-tab')->badge('Primary');
    }

    public function getTabComponents(): array
    {
        if (! $this->hasCustomTabs) {
            return parent::getTabComponents();
        }

        return [
            'view' => Tab::make('Overview')->id('overview-tab')->schema([EmbeddedSchema::make('overview')]),
            3 => $this->getRelationManagerTabComponent(3)?->id('configured-manager-tab'),
            '' => $this->getContentTabComponent(),
            'departments' => $this->getRelationManagerTabComponent('departments'),
            ...($this->isPreferencesRemoved ? [] : [
                'preferences' => Tab::make('Preferences')->id('preferences-tab')
                    ->hidden(fn (): bool => $this->isPreferencesHidden)
                    ->schema([
                        Form::make([EmbeddedSchema::make('preferencesForm')])
                            ->livewireSubmitHandler('savePreferences')
                            ->footer([
                                Actions::make([
                                    Action::make('savePreferences')->label('Save preferences')->submit('savePreferences'),
                                    Action::make('hidePreferences')->action('hidePreferences'),
                                    Action::make('removePreferences')->action(fn () => $this->removePreferences()),
                                ]),
                            ]),
                    ]),
            ]),
            9 => $this->getRelationManagerTabComponent(9),
        ];
    }

    public function getContentTabPosition(): ?ContentTabPosition
    {
        return $this->isContentAfter ? ContentTabPosition::After : null;
    }

    protected function getAllRelationManagers(): array
    {
        if (! $this->hasManagers) {
            return [];
        }

        $configuredManager = ContentTabsDepartmentsRelationManager::make(['heading' => 'Configured departments', 'lazy' => false]);

        return [
            3 => $this->hasGroupedManagers
                ? RelationGroup::make('Department group', [$configuredManager])->badge('Grouped')
                : $configuredManager,
            'departments' => ContentTabsDepartmentsRelationManager::class,
            9 => ContentTabsDepartmentsRelationManager::class,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)->components([
            TextInput::make('editNote')->id('edit-note')->required()->dehydrated(false),
            TextEntry::make('editStatus')->state(fn (): string => $this->isEditSaved ? 'Edit saved' : 'Edit not saved'),
        ]);
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->extraAttributes(['data-testid' => 'save-ticket']);
    }

    public function overview(Schema $schema): Schema
    {
        return $schema->components([TextEntry::make('summary')->state('Ticket overview')]);
    }

    public function preferencesForm(Schema $schema): Schema
    {
        return $schema->statePath('preferencesData')->components([
            TextInput::make('nickname')->id('preference-nickname')->required(),
            TextEntry::make('preferenceStatus')->state(fn (): string => $this->savedPreference ?: 'Preferences not saved'),
        ]);
    }

    public function savePreferences(): void
    {
        $this->savedPreference = $this->preferencesForm->getState()['nickname'];
    }

    public function hidePreferences(): void
    {
        $this->isPreferencesHidden = true;
    }

    public function removePreferences(): void
    {
        $this->isPreferencesRemoved = true;
    }

    protected function afterSave(): void
    {
        $this->isEditSaved = true;
    }
}
