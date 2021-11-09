<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;

class EditRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\CanResolveResourceRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $record;

    public $data;

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Edit';
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill($this->record->toArray());
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
                ->label('Save'),
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
}
