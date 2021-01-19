<?php

namespace Filament\Http\Livewire\Auth;

use Filament\Fields\Text;
use Filament\Filament;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Password,};
use Illuminate\Support\Str;
use Livewire\Component;

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

    public function fields()
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('filament::fields.labels.email')
                ->modelDirective('wire:model.lazy')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                ]),
            Text::make('password')
                ->type('password')
                ->label('filament::fields.labels.password')
                ->extraAttributes([
                    'required' => 'true',
                    'autofocus' => 'true',
                    'autocomplete' => 'new-password',
                ]),
            Text::make('password_confirmation')
                ->type('password')
                ->label('filament::fields.labels.newPassword')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
        ];
    }

    public function mount(Request $request, $token)
    {
        $this->email = $request->input('email');
        $this->token = $token;
    }

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

            return redirect()->to(route('filament.dashboard'));
        } else {
            $this->addError('email', __($status));
        }
    }

    protected function credentials()
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ];
    }

    public function render()
    {
        return view('filament::livewire.auth.reset-password')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.resetPassword')]);
    }
}
