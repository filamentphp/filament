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
        'password' => 'required|min:8',
    ];

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
                ->hint('[' . __('filament::auth.forgotPassword') . '](' . route('filament.password.forgot') . ')'),
            Checkbox::make('remember')
                ->label('Remember me'),
        ];
    }

    public function submit(Request $request)
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);

            return;
        }

        $data = $this->validate();

        if (Auth::guard('filament')->attempt($data, (bool) $this->remember)) {
            $this->clearRateLimiter();

            return redirect()->intended(Filament::home());
        }

        $this->addError('email', __('auth.failed'));
    }

    public function render()
    {
        return view('filament::livewire.auth.login')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.signin')]);
    }
}
