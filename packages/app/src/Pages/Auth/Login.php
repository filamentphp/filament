<?php

namespace Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\CardPage;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
class Login extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament::pages.auth.login';

    public $email = '';

    public $password = '';

    public $remember = false;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => __('filament::login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'])) {
            throw ValidationException::withMessages([
                'email' => __('filament::login.messages.failed'),
            ]);
        }

        return app(LoginResponse::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label(__('filament::login.fields.email.label'))
                    ->email()
                    ->required()
                    ->autocomplete(),
                TextInput::make('password')
                    ->label(__('filament::login.fields.password.label'))
                    ->password()
                    ->required(),
                Checkbox::make('remember')
                    ->label(__('filament::login.fields.remember.label')),
            ]);
    }

    public static function getName(): string
    {
        return 'filament.core.auth.login';
    }

    public function getTitle(): string
    {
        return __('filament::login.title');
    }
}
