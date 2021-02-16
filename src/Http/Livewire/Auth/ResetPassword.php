<?php

namespace Filament\Http\Livewire\Auth;

use Filament\Forms\Fields;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ResetPassword extends Component
{
    use HasForm;

    public $email;

    public $password;

    public $passwordConfirmation;

    public $token;

    public $user;

    public function fields()
    {
        return [
            Fields\Text::make('email')
                ->label('filament::fields.labels.email')
                ->email()
                ->nameAttribute('wire:model.lazy')
                ->autocomplete()
                ->required(),
            Fields\Text::make('password')
                ->label('filament::fields.labels.password')
                ->password()
                ->autofocus()
                ->autocomplete('new-password')
                ->required()
                ->minLength(8)
                ->confirmed(),
            Fields\Text::make('passwordConfirmation')
                ->label('filament::fields.labels.newPassword')
                ->password()
                ->autocomplete('new-password')
                ->required()
                ->password(),
        ];
    }

    public function getForm()
    {
        return Form::make($this->fields())
            ->context(static::class);
    }

    public function mount(Request $request, $token)
    {
        $this->email = $request->input('email');
        $this->token = $token;
    }

    public function submit()
    {
        $this->validate();

        $resetStatus = Password::broker('filament_users')
            ->reset(
                $this->only(['email', 'password', 'token']),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();

                    $this->user = $user;
                },
            );

        if (Password::PASSWORD_RESET !== $resetStatus) {
            $this->addError('email', __('filament::auth.' . $resetStatus));

            return;
        }

        Auth::guard('filament')->login($this->user, true);

        return redirect()->to(route('filament.dashboard'));
    }

    public function render()
    {
        return view('filament::.auth.reset-password')
            ->layout('filament::components.layouts.auth', ['title' => 'filament::auth.resetPassword']);
    }
}
