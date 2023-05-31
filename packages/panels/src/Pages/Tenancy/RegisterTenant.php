<?php

namespace Filament\Pages\Tenancy;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\CardPage;
use Filament\Pages\Concerns;
use Filament\Panel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * @property Form $form
 */
abstract class RegisterTenant extends CardPage
{
    use Concerns\HasRoutes;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::pages.tenancy.register-tenant';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?Model $tenant = null;

    abstract public static function getLabel(): string;

    public static function routes(Panel $panel): void
    {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->name('registration');
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return static::$routeMiddleware;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return $data;
    }

    public function register(): void
    {
        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $this->tenant = $this->handleRegistration($data);

            $this->form->model($this->tenant)->saveRelationships();

            $this->callHook('afterRegister');
        } catch (Halt $exception) {
            return;
        }

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        return $this->getModel()::create($data);
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl($this->tenant);
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->model($this->getModel())
                    ->statePath('data'),
            ),
        ];
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->label(static::getLabel())
            ->submit('register');
    }

    public function getModel(): string
    {
        return Filament::getTenantModel();
    }

    public function getTitle(): string
    {
        return static::getLabel();
    }

    public static function getSlug(): string
    {
        return static::$slug ?? 'new';
    }
}
