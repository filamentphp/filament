<x-filament::form submit="login" class="space-y-6">
    <x-filament::fields :fields="$this->fields()" />

    <x-filament::button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />
        {{ __('Login') }}
    </x-filament::button>

    <x-filament::message />
</x-filament::form>