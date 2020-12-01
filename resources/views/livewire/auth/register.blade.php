<x-filament::form submit="register">
    <x-filament::input-group-stacked field="name" for="name" :label="__('Name')" required>
        <x-filament::input field="name" wire:model.defer="name" id="name" required autofocus tabindex="1" />
        <x-slot name="hint">
            <a href="{{ route('filament.login') }}" class="text-current hover:text-blue-600" tabindex="6">
                &larr; {{ __('Back to login') }}
            </a>
        </x-slot>
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked field="email" for="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" field="email" wire:model.lazy="email" id="email" required autocomplete="email" tabindex="2" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked field="password" for="password" :label="__('New Password')" required>   
        <x-filament::input type="password" field="password" wire:model.defer="password" id="password" required autocomplete="new-password" tabindex="3" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked field="password_confirmation" for="password_confirmation" :label="__('Confirm New Password')" required>   
        <x-filament::input type="password" field="password_confirmation" wire:model.defer="password_confirmation" id="password_confirmation" required autocomplete="new-password" tabindex="4" />
    </x-filament::input-group-stacked>

    <x-filament::button type="submit" class="btn-primary w-full" wire:loading.attr="disabled" tabindex="5">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />
        {{ __('Register') }}
    </x-filament::button>
</x-filament::form>