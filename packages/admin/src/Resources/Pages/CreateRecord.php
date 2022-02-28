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
class CreateRecord extends Page implements HasFormActions
{
    use Concerns\UsesResourceForm;

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

    public function create(bool $another = false): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $this->record = $this->handleRecordCreation($data);

        $this->form->model($this->record)->saveRelationships();

        $this->callHook('afterCreate');

        if (filled($this->getCreatedNotificationMessage())) {
            $this->notify(
                'success',
                $this->getCreatedNotificationMessage(),
                isAfterRedirect: ! $another,
            );
        }

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->record::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $this->redirect($this->getRedirectUrl());
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return __('filament::resources/pages/create-record.messages.created');
    }

    public function createAndCreateAnother(): void
    {
        $this->create(another: true);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAndCreateAnotherFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): ButtonAction
    {
        return ButtonAction::make('create')
            ->label(__('filament::resources/pages/create-record.form.actions.create.label'))
            ->submit('create');
    }

    protected function getCreateAndCreateAnotherFormAction(): ButtonAction
    {
        return ButtonAction::make('createAnother')
            ->label(__('filament::resources/pages/create-record.form.actions.create_and_create_another.label'))
            ->action('createAndCreateAnother')
            ->color('secondary');
    }

    protected function getCancelFormAction(): ButtonAction
    {
        return ButtonAction::make('cancel')
            ->label(__('filament::resources/pages/create-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
    }

    protected function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament::resources/pages/create-record.title', [
            'label' => Str::title(static::getResource()::getLabel()),
        ]);
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            'form' => $this->makeForm()
                ->model(static::getModel())
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('edit') && $resource::canEdit($this->record)) {
            return $resource::getUrl('edit', ['record' => $this->record]);
        }

        if ($resource::hasPage('view') && $resource::canView($this->record)) {
            return $resource::getUrl('view', ['record' => $this->record]);
        }

        return $resource::getUrl('index');
    }

    protected function getMountedActionFormModel(): string
    {
        return static::getModel();
    }
}
