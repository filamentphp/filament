<?php

namespace Filament\Pages\Tenancy;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register;
use Filament\Pages\CardPage;
use Filament\Pages\Concerns;
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
     * @var view-string $view
     */
    protected static string $view = 'filament::pages.tenancy.register-tenant';

    public $data;

    public ?Model $tenant = null;

    abstract public static function getLabel(): string;

    public static function routes(Context $context): void
    {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($context))
            ->name('registration');
    }

    public static function getRouteMiddleware(Context $context): string | array
    {
        return static::$routeMiddleware;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

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

    protected function handleRegistration(array $data): Model
    {
        return $this->getModel()::create($data);
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl($this->tenant);
    }

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
