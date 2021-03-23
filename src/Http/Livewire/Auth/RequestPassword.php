<?php

namespace Filament\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class RequestPassword extends Component
{
    use HasForm;
    use WithRateLimiting;

    public $email;

    public function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('email')
                    ->label('filament::auth/request-password.form.email.label')
                    ->hint('[' . __('filament::auth/request-password.form.email.hint') . '](' . route('filament.auth.login') . ')')
                    ->email()
                    ->autofocus()
                    ->autocomplete('email')
                    ->required()
                    ->email(),
            ]);
    }

    public function render()
    {
        return view('filament::.auth.request-password')
            ->layout('filament::components.layouts.auth', ['title' => 'filament::auth/request-password.title']);
    }

    public function submit()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->addError('email', __('filament::auth/request-password.messages.throttled', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return;
        }

        $requestStatus = Password::broker('filament_users')->sendResetLink($this->validate());

        if (Password::RESET_LINK_SENT !== $requestStatus) {
            $this->addError('email', __("filament::auth/request-password.messages.{$requestStatus}"));

            return;
        }

        $this->dispatchBrowserEvent('notify', __("filament::auth/request-password.messages.{$requestStatus}"));
    }
}
