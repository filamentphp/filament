<x-filament::tabs :label="__('Profile')" tab="account">
    <x-slot name="tablist">
        <x-filament::tab id="account">
            {{ __('Account') }}
        </x-filament::tab>
    </x-slot>
    <x-filament::tab-panel id="account">
        <livewire:filament-account :user="Auth::user()" />
    </x-filament::tab-panel>
</x-filament::tabs>