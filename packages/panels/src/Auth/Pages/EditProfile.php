<?php

namespace Filament\Auth\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\VerifyEmailChange;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Pages\Concerns;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Js;
use Illuminate\Validation\Rules\Password;
use League\Uri\Components\Query;
use Throwable;

/**
 * @property-read Schema $form
 */
class EditProfile extends Page
{
    use Concerns\CanUseDatabaseTransactions;
    use Concerns\HasMaxWidth;
    use Concerns\HasTopbar;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static bool $isDiscovered = false;

    protected string $view;

    public function getLayout(): string
    {
        return static::$layout ?? (static::isSimple() ? 'filament-panels::components.layout.simple' : 'filament-panels::components.layout.index');
    }

    public static function isSimple(): bool
    {
        return Filament::isProfilePageSimple();
    }

    public function getView(): string
    {
        return $this->view ?? 'filament-panels::auth.pages.edit-profile';
    }

    public static function getLabel(): string
    {
        return static::$title ?? __('filament-panels::auth/pages/edit-profile.label');
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'profile';
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    protected function fillForm(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public static function registerRoutes(Panel $panel): void
    {
        if (filled(static::getCluster())) {
            Route::name(static::prependClusterRouteBaseName($panel, ''))
                ->prefix(static::prependClusterSlug($panel, ''))
                ->group(fn () => static::routes($panel));

            return;
        }

        static::routes($panel);
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        return $panel->generateRouteName('auth.' . static::getRelativeRouteName($panel));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');
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

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (Filament::hasEmailChangeVerification() && array_key_exists('email', $data)) {
            $this->sendEmailChangeVerification($record, $data['email']);

            unset($data['email']);
        }

        $record->update($data);

        return $record;
    }

    protected function sendEmailChangeVerification(Model $record, string $newEmail): void
    {
        if ($record->getAttributeValue('email') === $newEmail) {
            return;
        }

        $notification = app(VerifyEmailChange::class);
        $notification->url = Filament::getVerifyEmailChangeUrl($record, $newEmail);

        $verificationSignature = Query::new($notification->url)->get('signature');

        cache()->put($verificationSignature, true, ttl: now()->addHour());

        $record->notify(app(NoticeOfEmailChangeRequest::class, [/** @phpstan-ignore-line */
            'blockVerificationUrl' => Filament::getBlockEmailChangeVerificationUrl($record, $newEmail, $verificationSignature),
            'newEmail' => $newEmail,
        ]));

        Notification::route('mail', $newEmail)
            ->notify($notification);

        $this->getEmailChangeVerificationSentNotification($newEmail)?->send();

        $this->data['email'] = $record->getAttributeValue('email');
    }

    protected function getSavedNotification(): ?FilamentNotification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return FilamentNotification::make()
            ->success()
            ->title($title);
    }

    protected function getEmailChangeVerificationSentNotification(string $newEmail): ?FilamentNotification
    {
        return FilamentNotification::make()
            ->success()
            ->title(__('filament-panels::auth/pages/edit-profile.notifications.email_change_verification_sent.title', ['email' => $newEmail]))
            ->body(__('filament-panels::auth/pages/edit-profile.notifications.email_change_verification_sent.body', ['email' => $newEmail]));
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('filament-panels::auth/pages/edit-profile.notifications.saved.title');
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::auth/pages/edit-profile.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->live(debounce: 500);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::auth/pages/edit-profile.form.password.label'))
            ->validationAttribute(__('filament-panels::auth/pages/edit-profile.form.password.validation_attribute'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::default())
            ->showAllValidationMessages()
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::auth/pages/edit-profile.form.password_confirmation.label'))
            ->validationAttribute(__('filament-panels::auth/pages/edit-profile.form.password_confirmation.validation_attribute'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label(__('filament-panels::auth/pages/edit-profile.form.current_password.label'))
            ->validationAttribute(__('filament-panels::auth/pages/edit-profile.form.current_password.validation_attribute'))
            ->belowContent(__('filament-panels::auth/pages/edit-profile.form.current_password.below_content'))
            ->password()
            ->currentPassword(guard: Filament::getAuthGuard())
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
            ->dehydrated(false);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel(! static::isSimple())
            ->model($this->getUser())
            ->operation('edit')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
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

    protected function getCancelFormAction(): Action
    {
        return $this->backAction();
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::auth/pages/edit-profile.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function getFormActionsAlignment(): string | Alignment
    {
        return Alignment::Start;
    }

    public function getTitle(): string | Htmlable
    {
        return static::getLabel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? 'profile';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    /**
     * @deprecated Use `getCancelFormAction()` instead.
     */
    public function backAction(): Action
    {
        $url = filament()->getUrl();

        return Action::make('back')
            ->label(__('filament-panels::auth/pages/edit-profile.actions.cancel.label'))
            ->alpineClickHandler(
                FilamentView::hasSpaMode($url)
                    ? 'document.referrer ? window.history.back() : Livewire.navigate(' . Js::from($url) . ')'
                    : 'document.referrer ? window.history.back() : (window.location.href = ' . Js::from($url) . ')',
            )
            ->color('gray');
    }

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => $this->hasTopbar(),
            'maxWidth' => $this->getMaxWidth(),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                ...Arr::wrap($this->getMultiFactorAuthenticationContentComponent()),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->sticky((! static::isSimple()) && $this->areFormActionsSticky()),
            ]);
    }

    public function getMultiFactorAuthenticationContentComponent(): ?Component
    {
        if (! Filament::hasMultiFactorAuthentication()) {
            return null;
        }

        $user = Filament::auth()->user();

        return Section::make()
            ->label(__('filament-panels::auth/pages/edit-profile.multi_factor_authentication.label'))
            ->compact()
            ->divided()
            ->secondary()
            ->schema(collect(Filament::getMultiFactorAuthenticationProviders())
                ->sort(fn (MultiFactorAuthenticationProvider $multiFactorAuthenticationProvider): int => $multiFactorAuthenticationProvider->isEnabled($user) ? 0 : 1)
                ->map(fn (MultiFactorAuthenticationProvider $multiFactorAuthenticationProvider): Component => Group::make($multiFactorAuthenticationProvider->getManagementSchemaComponents())
                    ->statePath($multiFactorAuthenticationProvider->getId()))
                ->all());
    }

    public function getDefaultTestingSchemaName(): ?string
    {
        return 'form';
    }
}
