<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;

class CreateRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.create-record';

    public $record;

    public $data;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/create-record.breadcrumb');
    }

    public function mount(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
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

        $resource = static::getResource();

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
            $this->getCreateButtonFormAction(),
            $this->getCancelButtonFormAction(),
        ];
    }

    protected function getCreateButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('create')
            ->label(__('filament::resources/pages/create-record.form.actions.create.label'))
            ->submit();
    }

    protected function getCancelButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('cancel')
            ->label(__('filament::resources/pages/create-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
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
