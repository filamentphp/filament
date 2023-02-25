<?php

namespace Filament\Resources\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property Form $form
 */
class CreateRecord extends Page
{
    use InteractsWithFormActions;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::resources.pages.create-record';

    public ?Model $record = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?string $previousUrl = null;

    protected static bool $canCreateAnother = true;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/create-record.breadcrumb');
    }

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->record)->saveRelationships();

            $this->callHook('afterCreate');
        } catch (Halt $exception) {
            return;
        }

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->record::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $this->redirect($this->getRedirectUrl());
    }

    protected function getCreatedNotification(): ?Notification
    {
        $title = $this->getCreatedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($title);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->getCreatedNotificationMessage() ?? __('filament::resources/pages/create-record.messages.created');
    }

    /**
     * @deprecated Use `getCreatedNotificationTitle()` instead.
     */
    protected function getCreatedNotificationMessage(): ?string
    {
        return null;
    }

    public function createAnother(): void
    {
        $this->create(another: true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        if ($tenant = Filament::getTenant()) {
            $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }

    protected function associateRecordWithTenant(Model $record, Model $tenant): void
    {
        $relationshipName = Filament::getTenantOwnershipRelationshipName();

        if (! $record->isRelation($relationshipName)) {
            $pageClass = static::class;
            $recordClass = $record::class;

            throw new Exception("The model [{$recordClass}] does not have a relationship named [{$relationshipName}]. This relationship is required to associate the record with the tenant. You can change the relationship being used by passing it to the [ownershipRelationship] argument of the [tenant()] method in configuration. Alternatively, you can override the [associateRecordWithTenant()] method on the [{$pageClass}] class to associate the record with the tenant in a different way.");
        }

        $record->{$relationshipName}()->associate($tenant);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return array_merge(
            [$this->getCreateFormAction()],
            static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : [],
            [$this->getCancelFormAction()],
        );
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getSubmitFormAction(): Action
    {
        return $this->getCreateFormAction();
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('filament::resources/pages/create-record.form.actions.create_another.label'))
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('gray');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament::resources/pages/create-record.form.actions.cancel.label'))
            ->url($this->previousUrl ?? static::getResource()::getUrl())
            ->color('gray');
    }

    public function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament::resources/pages/create-record.title', [
            'label' => Str::headline(static::getResource()::getModelLabel()),
        ]);
    }

    public function form(Form $form): Form
    {
        return static::getResource()::form(
            $form
                ->operation('create')
                ->model($this->getModel())
                ->statePath('data')
                ->columns($this->hasInlineLabels() ? 1 : 2)
                ->inlineLabel($this->hasInlineLabels()),
        );
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('view') && $resource::canView($this->record)) {
            return $resource::getUrl('view', ['record' => $this->record]);
        }

        if ($resource::hasPage('edit') && $resource::canEdit($this->record)) {
            return $resource::getUrl('edit', ['record' => $this->record]);
        }

        return $resource::getUrl('index');
    }

    protected function getMountedActionFormModel(): string
    {
        return $this->getModel();
    }

    public static function canCreateAnother(): bool
    {
        return static::$canCreateAnother;
    }

    public static function disableCreateAnother(): void
    {
        static::$canCreateAnother = false;
    }
}
