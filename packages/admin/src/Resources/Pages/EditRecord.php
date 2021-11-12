<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;

class EditRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $record;

    public $data;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Edit';
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->getRecord($record);

        abort_unless(static::getResource()::canEdit($this->record), 403);

        $this->callHook('beforeFill');

        $this->form->fill($this->record->toArray());

        $this->callHook('afterFill');
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeSave');

        $this->record->update($data);

        $this->callHook('afterSave');

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
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

        $this->record->delete();

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        return [
            ButtonAction::make('view')
                ->label('View')
                ->url(fn () => $resource::getUrl('view', ['record' => $this->record]))
                ->color('secondary')
                ->hidden(! $resource::canView($this->record)),
            ButtonAction::make('delete')
                ->label('Delete')
                ->action('openDeleteModal')
                ->color('danger')
                ->hidden(! $resource::canDelete($this->record)),
        ];
    }

    protected function getDynamicTitle(): string
    {
        return ($recordTitle = $this->getRecordTitle()) ? "Edit {$recordTitle}" : static::getTitle();
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label('Save')
                ->submit(),
            ButtonAction::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl())
                ->color('secondary'),
        ];
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
        $resource = static::getResource();

        if (! $resource::canView($this->record)) {
            return null;
        }

        return $resource::getUrl('view', ['record' => $this->record]);
    }
}
