<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;

class ViewRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\CanResolveResourceRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.view-record';

    public $record;

    public $data;

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'View';
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill($this->record->toArray());
    }

    protected function canEdit(): bool
    {
        return true;
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('edit')
                ->label('Edit')
                ->url(static::getResource()::getEditUrl($this->record))
                ->hidden(! $this->canEdit()),
        ];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->disabled()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }
}
