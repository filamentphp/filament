<x-filament::tabs :label="__('Profile')" tab="account">
    <x-slot name="tablist">
        <x-filament::tab id="account">
            {{ __('Account') }}
        </x-filament::tab>
        @if (Filament\Features::hasPermissions())
            <x-filament::tab id="permissions">
                {{ __('Permissions') }}
            </x-filament::tab>
        @endif 
    </x-slot>
    <x-filament::tab-panel id="account">
        <livewire:filament-account :user="Auth::user()" />
    </x-filament::tab-panel>
    @if (Filament\Features::hasPermissions())
        <x-filament::tab-panel id="permissions">
            Permissions...
        </x-filament::tab-panel>
    @endif
</x-filament::tabs>