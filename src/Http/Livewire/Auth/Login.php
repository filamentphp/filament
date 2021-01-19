<?php

namespace Filament\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Fields\{Checkbox, Text};
use Filament\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth};
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    use WithRateLimiting;

    public $email;

    public $password;

    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function authenticate()
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

    public function fields()
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('filament::fields.labels.email')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                    'autofocus' => 'true',
                ]),
            Text::make('password')
                ->type('password')
                ->label('filament::fields.labels.password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'current-password',
                ])
                ->hint('[' . __('filament::auth.forgotPassword') . '](' . route('filament.auth.password.forgot') . ')'),
            Checkbox::make('remember')
                ->label('Remember me'),
        ];
    }

    public function render()
    {
        return view('filament::livewire.auth.login')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.signin')]);
    }
}
