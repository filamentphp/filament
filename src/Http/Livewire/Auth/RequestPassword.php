<?php

namespace Filament\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Concerns;
use Filament\Forms\Fields;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class RequestPassword extends Component
{
    use Concerns\HasTitle;
    use Concerns\SendsToastNotifications;
    use HasForm;
    use WithRateLimiting;

    public $email;

    public function fields()
    {
        return [
            Fields\Text::make('email')
                ->label('filament::fields.labels.email')
                ->hint('[' . __('filament::auth.backToLogin') . '](' . route('filament.auth.login') . ')')
                ->email()
                ->autofocus()
                ->autocomplete('email')
                ->required()
                ->email(),
        ];
    }

    public function getForm()
    {
        return Form::make($this->getFields())
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

        $requestStatus = Password::broker('filament_users')->sendResetLink($this->validate());

        if (Password::RESET_LINK_SENT !== $requestStatus) {
            $this->addError('email', __('filament::auth.' . $requestStatus));

            return;
        }

        $this->notify(__('filament::auth.' . $requestStatus));
    }

    public function render()
    {
        return view('filament::.auth.request-password')
            ->layout('filament::components.layouts.auth', ['title' => 'filament::auth.resetPassword']);
    }
}
