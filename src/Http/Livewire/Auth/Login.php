<?php

namespace Filament\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Action;
use Filament\Fields;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Action
{
    use WithRateLimiting;

    public $email;

    public $password;

    public $remember = false;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => 'required',
    ];

    public function fields()
    {
        return [
            Fields\Text::make('email')
                ->type('email')
                ->label('filament::fields.labels.email')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                    'autofocus' => 'true',
                ]),
            Fields\Text::make('password')
                ->type('password')
                ->label('filament::fields.labels.password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'current-password',
                ])
                ->hint('[' . __('filament::auth.requestPassword') . '](' . route('filament.auth.password.request') . ')'),
            Fields\Checkbox::make('remember')
                ->label('Remember me'),
        ];
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

        if (! Auth::guard('filament')->attempt($this->validate(), $this->remember)) {
            $this->addError('email', __('auth.failed'));

            return;
        }

        return redirect()->intended(route('filament.dashboard'));
    }

    public function render()
    {
        return view('filament::.auth.login')
            ->layout('filament::layouts.auth', ['title' => 'filament::auth.signin']);
    }
}
