<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Password,
};
use Livewire\Component;
use Filament\Facades\Filament;
use Filament\Fields\Text;

class ResetPassword extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public $token;
    public $user;

    protected $rules = [
        'email' => 'required|string|email',
        'password' => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required|string|same:password',
    ];

    public function mount(Request $request, $token): void
    {
        $this->token = $token;
        $this->email = $request->input('email');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function submit()
    {
        $this->validate();
        $status = Password::reset(
            $this->credentials(),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(60));
                event(new PasswordReset($user));
                $this->user = $user;
            }
        );
        
        if ($status === Password::PASSWORD_RESET) {
            Auth::login($this->user);
            return redirect()->to(Filament::home());
        } else {
            $this->addError('email', __($status));
        }
    }

    /**
     * @return array
     *
     * @psalm-return array{0: mixed, 1: mixed, 2: mixed}
     */
    public function fields(): array
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->modelDirective('wire:model.lazy')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                ]),
            Text::make('password')
                ->type('password')
                ->label('Password')
                ->extraAttributes([
                    'required' => 'true',
                    'autofocus' => 'true',
                    'autocomplete' => 'new-password',
                ]),
            Text::make('password_confirmation')
                ->type('password')
                ->label('Confirm New Password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('filament::livewire.auth.reset-password')
            ->layout('filament::layouts.auth', ['title' => __('Reset Password')]);
    }

    /**
     * @return array
     *
     * @psalm-return array{token: mixed, email: mixed, password: mixed, password_confirmation: mixed}
     */
    protected function credentials(): array
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ];
    }
}