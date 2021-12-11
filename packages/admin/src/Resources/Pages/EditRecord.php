<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;

class EditRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $record;

    public $data;

    public $activeRelationManager = null;

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

        $this->activeRelationManager = $this->getResource()::getRelations()[0] ?? null;
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill($this->record->toArray());

        $this->callHook('afterFill');
    }

    public function save(bool $shouldRedirect = true): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $data = $this->mutateFormDataBeforeSave($data);

        $this->callHook('beforeSave');

        $this->record->update($data);

        $this->callHook('afterSave');

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl);
        } else {
            $this->notify('success', __('filament::resources/pages/edit-record.messages.saved'));
        }
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

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        return array_merge(
            ($resource::canView($this->record) ? [$this->getViewButtonAction()] : []),
            ($resource::canDelete($this->record) ? [$this->getDeleteButtonAction()] : []),
        );
    }

    protected function getViewButtonAction(): ButtonAction
    {
        return ButtonAction::make('view')
            ->label(__('filament::resources/pages/edit-record.actions.view.label'))
            ->url(fn () => static::getResource()::getUrl('view', ['record' => $this->record]))
            ->color('secondary');
    }

    protected function getDeleteButtonAction(): ButtonAction
    {
        return ButtonAction::make('delete')
            ->label(__('filament::resources/pages/edit-record.actions.delete.label'))
            ->action('openDeleteModal')
            ->color('danger');
    }

    protected function getTitle(): string
    {
        return static::$title ?? (($recordTitle = $this->getRecordTitle()) ? __('filament::resources/pages/edit-record.title', ['label' => $recordTitle]) : parent::getTitle());
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveButtonFormAction(),
            $this->getCancelButtonFormAction(),
        ];
    }

    protected function getSaveButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('save')
            ->label(__('filament::resources/pages/edit-record.form.actions.save.label'))
            ->submit();
    }

    protected function getCancelButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('cancel')
            ->label(__('filament::resources/pages/edit-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }
}
