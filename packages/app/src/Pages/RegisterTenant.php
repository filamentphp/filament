<?php

namespace Filament\Pages;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

/**
 * @property Form $form
 */
abstract class RegisterTenant extends Component implements HasForms
{
    use InteractsWithForms;

    public $data;

    public ?Model $tenant = null;

    protected static string | array $routeMiddleware = [];

    protected static string $slug = 'new';

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

    public static function getSlug(): string
    {
        return static::$slug;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return $data;
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
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

    protected function getRedirectUrl(): string
    {
        return Filament::getUrl($this->tenant);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->model($this->getModel())
                    ->statePath('data')
                    ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
            ),
        ];
    }

    public function getModel(): string
    {
        return Filament::getTenantModel();
    }

    public function render(): View
    {
        return view('filament::pages.tenancy.register-tenant')
            ->layout('filament::components.layouts.card', [
                'title' => static::getLabel(),
            ]);
    }
}
