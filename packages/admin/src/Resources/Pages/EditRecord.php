<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\View\Components\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\CanResolveResourceRecord;
    use Concerns\HasRecordBreadcrumb;
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
        $this->record = $this->resolveRecord($record);

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

    protected function canDelete(): bool
    {
        return true;
    }

    protected function canView(): bool
    {
        return true;
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('view')
                ->label('View')
                ->url(static::getResource()::getViewUrl($this->record))
                ->color('secondary')
                ->hidden((! static::getResource()::hasViewPage()) || (! $this->canView())),
            ButtonAction::make('delete')
                ->label('Delete')
                ->color('danger')
                ->hidden(! $this->canDelete()),
        ];
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
        if (! static::getResource()::hasViewPage()) {
            return null;
        }

        return static::getResource()::getViewUrl($this->record);
    }
}
