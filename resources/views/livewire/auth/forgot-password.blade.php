<form wire:submit.prevent="sendEmail" class="space-y-4">
    <x-filament::message />
    <x-filament::input-group-stacked name="email" :label="__('filament::auth.labels.email')" required>
        <x-filament::input type="email" name="email" wire:model.defer="email" id="email" required autocomplete="email" autofocus />
        <x-slot name="hint">
            <a href="{{ route('filament.login') }}" class="text-current hover:text-blue">
                &larr; {{ __('Back to login') }}
            </a>
        </x-slot>
    </x-filament::input-group-stacked>

    <x-filament::button type="submit" class="w-full">
        {{ __('Send Password Reset Link') }}
    </x-filament::button>
</form>