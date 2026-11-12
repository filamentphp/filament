<?php

namespace Filament\Resources\Pages;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\EditConflictHandler;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Resources\Events\RecordSaved;
use Filament\Resources\Events\RecordUpdated;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Js;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

use function Livewire\store;

/**
 * @template TModel of Model = Model
 *
 * @property-read Schema $content
 * @property-read Schema $form
 */
class EditRecord extends Page
{
    use Concerns\HasRelationManagers {
        getContentTabComponent as getBaseContentTabComponent;
    }
    use Concerns\InteractsWithRecord {
        getRecord as getBaseRecord;
        refreshRecordPage as protected refreshBaseRecordPage;
    }
    use Concerns\ResolvesFormConflicts;
    use HasUnsavedDataChangesAlert {
        rememberData as rememberBaseData;
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    #[Locked]
    public ?string $previousUrl = null;

    #[Unexposed]
    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve(PanelsIconAlias::RESOURCES_PAGES_EDIT_RECORD_NAVIGATION_ITEM)
            ?? Heroicon::OutlinedPencilSquare;
    }

    #[Unexposed]
    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/edit-record.breadcrumb');
    }

    #[Unexposed]
    public static function getNavigationLabel(): string | Htmlable
    {
        if (filled(static::$navigationLabel)) {
            return static::$navigationLabel;
        }

        return __('filament-panels::resources/pages/edit-record.navigation_label');
    }

    #[Unexposed]
    public function getContentTabLabel(): ?string
    {
        return __('filament-panels::resources/pages/edit-record.content.tab.label');
    }

    #[Unexposed]
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        static::getResource()::authorizeEdit($this->getRecord());
    }

    #[Unexposed]
    public function hydrate(): void
    {
        $this->authorizeAccess();
    }

    protected function fillForm(): void
    {
        /** @internal Read the DocBlock above the following method. */
        $this->fillFormWithDataAndCallHooks($this->getRecord());
    }

    /**
     * @internal Never override or call this method. If you completely override `fillForm()`, copy the contents of this method into your override.
     *
     * @param  array<string, mixed>  $extraData
     */
    protected function fillFormWithDataAndCallHooks(Model $record, array $extraData = []): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill([
            ...$record->attributesToArray(),
            ...$extraData,
        ]);

        $this->form->fill($data);

        $this->callHook('afterFill');

        if ($this->getFormEditConflictHandler()) {
            $this->baseFormState = $this->getFormStateForStaleEditComparison();
            $this->staleFormState = null;
        }
    }

    /**
     * @param  array<string>  $statePaths
     */
    #[Unexposed]
    public function refreshFormData(array $statePaths): void
    {
        $this->form->fillPartially(
            $this->mutateFormDataBeforeFill($this->getRecord()->attributesToArray()),
            $statePaths,
        );

        if ($this->getFormEditConflictHandler()) {
            $this->acceptFormStateBaseline($this->getFormStateForStaleEditComparison($this->getRecord()), $statePaths);
        }
    }

    protected function getFormEditConflictHandler(): ?EditConflictHandler
    {
        return null;
    }

    protected function rememberData(): void
    {
        $this->rememberBaseData();

        if ($this->getFormEditConflictHandler() && $this->hasUnsavedDataChangesAlert()) {
            // Keep the raw alert baseline, including non-dehydrated values and
            // component item keys, separate from normalized conflict state.
            $this->savedFormData = $this->data ?? [];
        }
    }

    #[Exposed]
    public function refreshRecordPage(bool $shouldRefreshForm = true): ?string
    {
        $title = $this->refreshBaseRecordPage();

        if (($title !== null) && $shouldRefreshForm && $this->getFormEditConflictHandler() && ($this->staleFormState === null)) {
            $handler = $this->getFormEditConflictHandler();
            $comparison = $this->getFormStateComparison()->ignoredStatePaths([]);
            $this->baseFormState = $handler->acceptIncomingState($this->form, $comparison, $this->formConflictPreviewState['current']);
            $this->formConflictPreviewState = [];

            if ($this->hasUnsavedDataChangesAlert()) {
                $paths = array_merge([], ...array_values(array_intersect_key($comparison->getStatePathGroups(), array_flip($comparison->getIncomingPaths()))));
                $this->savedFormData = $handler->acceptBaseline($this->savedFormData, $this->data ?? [], $paths);
                $this->savedDataHash = md5((string) str(json_encode($this->savedFormData, JSON_UNESCAPED_UNICODE))->replace('\\', ''));
            }
        }

        return $title;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Security: All non-`$hidden` model attributes are sent to the
        // browser via Livewire. Override this to `unset()` sensitive
        // attributes (API keys, internal flags, etc.). Only form
        // field attributes are writable — not a mass assignment
        // issue, but a data exposure concern.

        return $data;
    }

    #[Exposed]
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $comparison = $this->getFormEditConflictHandler() ? $this->getFormStateComparison() : null;

            if ($comparison && (! $this->checkForStaleEdit($comparison))) {
                $this->rollBackDatabaseTransaction();

                return;
            }

            $this->callHook('beforeValidate');

            $data = $this->form->getState(afterValidate: function (): void {
                $this->callHook('afterValidate');

                $this->callHook('beforeSave');
            });

            if ($comparison) {
                $data = $this->getFormEditConflictHandler()->preserveUneditedNestedData($data, $this->getRecord(), $comparison);
            }

            $data = $this->mutateFormDataBeforeSave($data);

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');
            Event::dispatch(RecordUpdated::class, ['record' => $this->record, 'data' => $data, 'page' => $this]);
            Event::dispatch(RecordSaved::class, ['record' => $this->record, 'data' => $data, 'page' => $this]);

            if ($comparison) {
                $this->baseFormState = $this->getFormStateForStaleEditComparison($this->getLockedRecord());
                $this->staleFormState = null;
            }
        } catch (Halt $exception) {
            $exception->shouldRollBackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
        }

        if (store($this)->has('recordSaveOperation')) {
            if (! store($this)->has('redirect')) {
                // Hooks and after-commit callbacks may have written through
                // another model instance. Reuse native scope and authorization
                // before acknowledging that this response replaces a refresh.
                $this->hasResolvedParentRecordForRequest = false;
                $this->mountParentRecord();

                $resource = static::getResource();
                $routeKeyName = $resource::getRecordRouteKeyName() ?? $this->getRecord()->getRouteKeyName();

                if ($routeKeyName !== $this->getRecord()->getKeyName()) {
                    $query = $resource::getRecordRouteBindingEloquentQuery()->useWritePdo()->whereKey($this->getRecord()->getKey());

                    if ($parentRecord = $this->getParentRecord()) {
                        $query = $resource::scopeEloquentQueryToParent($query, $parentRecord);
                    }

                    // Refresh a mutable route key by stable identity before
                    // retaining the native binding and authorization checks.
                    $this->record = $query->firstOrFail();
                }

                $this->hasResolvedRecordForRequest = false;
                $this->resolveScopedModelProperties();
                $this->authorizeAccess();
                $this->form->model($this->getRecord());
            }

            store($this)->set('recordSaveSucceeded', true);
        }
    }

    #[Unexposed]
    public function saveFormComponentOnly(Component $component): void
    {
        $this->authorizeAccess();
        $hasStaleEdit = false;

        try {
            $this->beginDatabaseTransaction();

            $comparison = null;

            if ($this->getFormEditConflictHandler()) {
                $comparison = $this->getFormStateComparisonForComponent($component);

                if (! $this->checkForStaleEdit($comparison)) {
                    $hasStaleEdit = true;

                    throw new Halt;
                }
            }

            $this->callHook('beforeValidate');

            $oldContainer = $component->getContainer();

            $data = Schema::make($component->getLivewire())
                ->components([$component])
                ->model($component->getRecord())
                ->operation($oldContainer->getOperation())
                ->statePath('data')
                ->getState();

            $component->container($oldContainer);

            $this->callHook('afterValidate');

            if ($comparison) {
                $data = $this->getFormEditConflictHandler()->preserveUneditedNestedData($data, $this->getRecord(), $comparison);
            }

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');

            if ($comparison) {
                $currentState = $this->getFormStateForStaleEditComparison($this->getLockedRecord());

                foreach ($comparison->getStatePathGroups() as $statePaths) {
                    $this->acceptFormStateBaseline($currentState, $statePaths);
                }

                $this->staleFormState = null;
            }
        } catch (Halt $exception) {
            if ($hasStaleEdit) {
                $this->rollBackDatabaseTransaction();

                throw $exception;
            }

            $exception->shouldRollBackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();
    }

    protected function getSavedNotification(): ?Notification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($title);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('filament-panels::resources/pages/edit-record.notifications.saved.title');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    #[Unexposed]
    public function getDefaultActionSchemaResolver(Action $action): ?Closure
    {
        return match (true) {
            $action instanceof CreateAction => fn (Schema $schema): Schema => static::getResource()::form($schema->hasCustomColumns() ? $schema : $schema->columns(2)),
            $action instanceof EditAction => fn (Schema $schema): Schema => $schema->components([EmbeddedSchema::make('form')]),
            $action instanceof ViewAction => fn (Schema $schema): Schema => static::getResource()::infolist(static::getResource()::form($schema->hasCustomColumns() ? $schema : $schema->columns(2))),
            default => null,
        };
    }

    #[Unexposed]
    public function getTitle(): string | Htmlable
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/edit-record.title', [
            'label' => $this->getRecordTitle(),
        ]);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        $hasFormWrapper = $this->hasFormWrapper();

        return Action::make('save')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->submit($hasFormWrapper ? $this->getSubmitFormLivewireMethodName() : null)
            ->action($hasFormWrapper ? null : $this->getSubmitFormLivewireMethodName())
            ->keyBindings(['mod+s']);
    }

    protected function getSubmitFormAction(): Action
    {
        return $this->getSaveFormAction();
    }

    protected function getSubmitFormLivewireMethodName(): string
    {
        return 'save';
    }

    protected function getCancelFormAction(): Action
    {
        $url = $this->previousUrl ?? $this->getResourceUrl();

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler(
                FilamentView::hasSpaMode($url)
                    ? 'document.referrer ? window.history.back() : Livewire.navigate(' . Js::from($url) . ')'
                    : 'document.referrer ? window.history.back() : (window.location.href = ' . Js::from($url) . ')',
            )
            ->color('gray');
    }

    #[Unexposed]
    public function defaultForm(Schema $schema): Schema
    {
        if (! $schema->hasCustomColumns()) {
            $schema->columns($this->hasInlineLabels() ? 1 : 2);
        }

        return $schema
            ->inlineLabel($this->hasInlineLabels())
            ->model($this->getRecord())
            ->operation('edit')
            ->statePath('data');
    }

    #[Unexposed]
    public function form(Schema $schema): Schema
    {
        return static::getResource()::form($schema);
    }

    #[Unexposed]
    public function getContentTabComponent(): Tab
    {
        return $this->getBaseContentTabComponent()
            ->schema([
                $this->getFormContentComponent(),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        $resource = static::getResource();

        if (
            filled($defaultRedirect = Filament::getResourceEditPageRedirect()) &&
            $resource::hasPage($defaultRedirect) &&
            (($defaultRedirect !== 'view') || $resource::canView($this->getRecord()))
        ) {
            return $this->getResourceUrl($defaultRedirect, $this->getRedirectUrlParameters());
        }

        try {
            $this->authorizeAccess();

            return null;
        } catch (AuthorizationException | HttpExceptionInterface) {
            // Do nothing.
        }

        if ($resource::hasPage('view') && $resource::canView($this->getRecord())) {
            return $this->getResourceUrl('view', $this->getRedirectUrlParameters());
        }

        return $this->getResourceUrl(parameters: $this->getRedirectUrlParameters());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRedirectUrlParameters(): array
    {
        return [];
    }

    #[Unexposed]
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return parent::shouldRegisterNavigation($parameters) && static::getResource()::canEdit($parameters['record']);
    }

    #[Unexposed]
    public function content(Schema $schema): Schema
    {
        if ($this->hasCombinedRelationManagerTabsWithContent()) {
            return $schema
                ->components([
                    $this->getRelationManagersContentComponent(),
                ]);
        }

        return $schema
            ->components([
                $this->getFormContentComponent(),
                $this->getRelationManagersContentComponent(),
            ]);
    }

    #[Unexposed]
    public function getFormContentComponent(): Component
    {
        if (! $this->hasFormWrapper()) {
            return Group::make([
                EmbeddedSchema::make('form'),
                $this->getFormActionsContentComponent(),
            ]);
        }

        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler($this->getSubmitFormLivewireMethodName())
            ->footer([
                $this->getFormActionsContentComponent(),
            ]);
    }

    #[Unexposed]
    public function getFormActionsContentComponent(): Component
    {
        return Actions::make($this->getFormActions())
            ->registerActions($this->getFormEditConflictHandler() ? [$this->getResolveFormConflictsAction()] : [])
            ->alignment($this->getFormActionsAlignment())
            ->fullWidth($this->hasFullWidthFormActions())
            ->sticky($this->areFormActionsSticky())
            ->key('form-actions');
    }

    #[Unexposed]
    public function hasFormWrapper(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    #[Unexposed]
    public function getPageClasses(): array
    {
        return [
            'fi-resource-edit-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug(Filament::getCurrentOrDefaultPanel())),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    /**
     * @return TModel
     */
    #[Unexposed]
    public function getRecord(): Model
    {
        return $this->getBaseRecord();
    }
}
