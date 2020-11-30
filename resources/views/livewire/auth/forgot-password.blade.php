<x-filament::form submit="sendEmail">
    <x-filament::input-group-stacked name="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" name="email" wire:model.defer="email" id="email" required autocomplete="email" autofocus tabindex="1" />
        <x-slot name="hint">
            <a href="{{ route('filament.login') }}" class="text-current hover:text-blue-600" tabindex="3">
                &larr; {{ __('Back to login') }}
            </a>
        </x-slot>
    </x-filament::input-group-stacked>

    <x-filament::button type="submit" class="btn-primary w-full" wire:loading.attr="disabled" tabindex="2">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />
        {{ __('Send Password Reset Link') }}
    </x-filament::button>

    <x-filament::message />
</x-filament::form>