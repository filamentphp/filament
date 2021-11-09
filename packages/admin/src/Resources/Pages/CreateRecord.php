<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\View\Components\Actions\ButtonAction;

class CreateRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.create-record';

    public $data;

    protected ?Form $resourceForm = null;

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Create';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('create')
                ->label('Create'),
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
                ->model(static::getResource()::getModel())
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }
}
