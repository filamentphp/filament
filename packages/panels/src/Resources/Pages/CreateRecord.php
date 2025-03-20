<?php

namespace Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Support\Js;
use Throwable;

use function Filament\Support\is_app_url;

/**
 * @property Form $form
 */
class CreateRecord extends Page
{
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;
    use InteractsWithFormActions;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::resources.pages.create-record';

    public ?Model $record = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?string $previousUrl = null;

    protected static bool $canCreateAnother = true;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/create-record.breadcrumb');
    }

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
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
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->getRecord())->saveRelationships();

            $this->callHook('afterCreate');
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->getRecord()::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $redirectUrl = $this->getRedirectUrl();

        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
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
        return $this->getCreatedNotificationMessage() ?? __('filament-panels::resources/pages/create-record.notifications.created.title');
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

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }

    protected function associateRecordWithTenant(Model $record, Model $tenant): Model
    {
        $relationship = static::getResource()::getTenantRelationship($tenant);

        if ($relationship instanceof (class_exists(HasOneOrManyThrough::class) ? HasOneOrManyThrough::class : HasManyThrough::class)) {
            $record->save();

            return $record;
        }

        return $relationship->save($record);
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
        return [
            $this->getCreateFormAction(),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
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
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create_another.label'))
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('gray');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? static::getResource()::getUrl()) . ')')
            ->color('gray');
    }

    public function getTitle(): string | Htmlable
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/create-record.title', [
            'label' => static::getResource()::getTitleCaseModelLabel(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(static::getResource()::form(
                $this->makeForm()
                    ->operation('create')
                    ->model($this->getModel())
                    ->statePath($this->getFormStatePath())
                    ->columns($this->hasInlineLabels() ? 1 : 2)
                    ->inlineLabel($this->hasInlineLabels()),
            )),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('view') && $resource::canView($this->getRecord())) {
            return $resource::getUrl('view', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
        }

        if ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
            return $resource::getUrl('edit', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
        }

        return $resource::getUrl('index');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRedirectUrlParameters(): array
    {
        return [];
    }

    protected function getMountedActionFormModel(): Model | string | null
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

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }
}
