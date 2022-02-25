<?php

namespace Filament\Resources\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Contracts\HasFormActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property ComponentContainer $form
 */
class EditRecord extends Page implements HasFormActions
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $record;

    public $data;

    protected $queryString = [
        'activeRelationManager',
    ];

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/edit-record.breadcrumb');
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->getRecord($record);

        abort_unless(static::getResource()::canEdit($this->record), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->record->toArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(bool $shouldRedirect = true): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $data = $this->mutateFormDataBeforeSave($data);

        $this->callHook('beforeSave');

        $this->handleRecordUpdate($this->record, $data);

        $this->callHook('afterSave');

        $shouldRedirect = $shouldRedirect && ($redirectUrl = $this->getRedirectUrl());

        $this->notify(
            'success',
            $this->getSavedNotificationMessage(),
            isAfterRedirect: $shouldRedirect,
        );

        if ($shouldRedirect) {
            $this->redirect($redirectUrl);
        }
    }

    protected function getSavedNotificationMessage(): string
    {
        return __('filament::resources/pages/edit-record.messages.saved');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public function openDeleteModal(): void
    {
        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'delete',
        ]);
    }

    public function delete(): void
    {
        abort_unless(static::getResource()::canDelete($this->record), 403);

        $this->callHook('beforeDelete');

        $this->record->delete();

        $this->callHook('afterDelete');

        $this->notify(
            'success',
            $this->getDeletedNotificationMessage(),
            isAfterRedirect: true,
        );

        $this->redirect($this->getDeleteRedirectUrl());
    }

    protected function getDeletedNotificationMessage(): string
    {
        return __('filament::resources/pages/edit-record.actions.delete.messages.deleted');
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        return array_merge(
            (($resource::hasPage('view') && $resource::canView($this->record)) ? [$this->getViewAction()] : []),
            ($resource::canDelete($this->record) ? [$this->getDeleteAction()] : []),
        );
    }

    protected function getViewAction(): ButtonAction
    {
        return ButtonAction::make('view')
            ->label(__('filament::resources/pages/edit-record.actions.view.label'))
            ->url(fn () => static::getResource()::getUrl('view', ['record' => $this->record]))
            ->color('secondary');
    }

    protected function getDeleteAction(): ButtonAction
    {
        return ButtonAction::make('delete')
            ->label(__('filament::resources/pages/edit-record.actions.delete.label'))
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/pages/edit-record.actions.delete.modal.heading', ['label' => $this->getRecordTitle() ?? static::getResource()::getLabel()]))
            ->modalSubheading(__('filament::resources/pages/edit-record.actions.delete.modal.subheading'))
            ->modalButton(__('filament::resources/pages/edit-record.actions.delete.modal.buttons.delete.label'))
            ->action('delete')
            ->color('danger');
    }

    protected function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        if (filled($recordTitle = $this->getRecordTitle())) {
            return __('filament::resources/pages/edit-record.title', [
                'label' => $recordTitle,
            ]);
        }

        return __('filament::resources/pages/edit-record.title', [
            'label' => Str::title(static::getResource()::getLabel()),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getSaveFormAction(): ButtonAction
    {
        return ButtonAction::make('save')
            ->label(__('filament::resources/pages/edit-record.form.actions.save.label'))
            ->submit('save');
    }

    protected function getCancelFormAction(): ButtonAction
    {
        return ButtonAction::make('cancel')
            ->label(__('filament::resources/pages/edit-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            'form' => $this->makeForm()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function getDeleteRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->record;
    }
}
