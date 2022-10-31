<form wire:submit.prevent="resetPassword" class="space-y-8">
    {{ $this->form }}

    <x-filament::button type="submit" form="resetPassword" class="w-full">
        {{ __('filament::pages/auth/password-reset/reset-password.buttons.reset.label') }}
    </x-filament::button>
</form>
