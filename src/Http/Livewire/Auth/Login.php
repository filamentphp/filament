<?php

namespace Filament\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Filament;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Livewire\Component;

class Login extends Component
{
    use HasForm;
    use WithRateLimiting;

    public $email;

    public $password;

    public $remember = false;

    public function getForm()
    {
        return Form::make()
            ->context(static::class)
            ->schema([
                Components\TextInput::make('email')
                    ->label('filament::auth/login.form.email.label')
                    ->email()
                    ->autofocus()
                    ->autocomplete('email')
                    ->required(),
                Components\TextInput::make('password')
                    ->label('filament::auth/login.form.password.label')
                    ->hint('[' . __('filament::auth/login.form.password.hint') . '](' . route('filament.auth.password.request') . ')')
                    ->password()
                    ->autocomplete('current-password')
                    ->required(),
                Components\Checkbox::make('remember')
                    ->label('filament::auth/login.form.remember.label'),
            ]);
    }

    public function submit()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->addError('email', __('filament::auth/login.messages.throttled', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return;
        }

        $this->validate();

        if (! Filament::auth()->attempt($this->only(['email', 'password']), $this->remember)) {
            $this->addError('email', __('filament::auth/login.messages.failed'));

            return;
        }

        return redirect()->intended(route('filament.dashboard'));
    }

    public function render()
    {
        return view('filament::auth.login')
            ->layout('filament::components.layouts.auth', [
                'title' => 'filament::auth/login.title',
            ]);
    }
}
