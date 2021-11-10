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

    public $record;

    public $data;

    protected ?Form $resourceForm = null;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Create';
    }

    public function mount(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);

        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function create(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeCreate');

        $this->record = static::getModel()::create($data);

        $this->form->model($this->record)->saveRelationships();

        $this->callHook('afterCreate');

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('create')
                ->label('Create')
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
                ->model(static::getModel())
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        $resource = static::getResource();

        if ($resource::canView($this->record)) {
            return $resource::getUrl('view', ['record' => $this->record]);
        }

        if ($resource::canEdit($this->record)) {
            return $resource::getUrl('edit', ['record' => $this->record]);
        }

        return null;
    }
}
