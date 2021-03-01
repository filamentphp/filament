<?php

namespace Filament\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Support\Facades\Auth;
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
            ->schema([
                Components\TextInput::make('email')
                    ->label('filament::fields.labels.email')
                    ->email()
                    ->autofocus()
                    ->autocomplete('email')
                    ->required(),
                Components\TextInput::make('password')
                    ->label('filament::fields.labels.password')
                    ->hint('[' . __('filament::auth.requestPassword') . '](' . route('filament.auth.password.request') . ')')
                    ->password()
                    ->autocomplete('current-password')
                    ->required(),
                Components\Checkbox::make('remember')->label('Remember me'),
            ])
            ->context(static::class);
    }

    public function submit()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->addError('email', __('auth.throttle', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return;
        }

        $this->validate();

        if (! Auth::guard('filament')->attempt($this->only(['email', 'password']), $this->remember)) {
            $this->addError('email', __('auth.failed'));

            return;
        }

        return redirect()->intended(route('filament.dashboard'));
    }

    public function render()
    {
        return view('filament::.auth.login', [
            'title' => 'Login',
        ])
            ->layout('filament::components.layouts.auth', ['title' => 'filament::auth.signin']);
    }
}
