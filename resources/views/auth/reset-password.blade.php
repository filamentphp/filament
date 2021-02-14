<x-filament::form :fields="$this->getForm()->fields" class="space-y-6">
    <x-filament::button type="submit" color="primary" class="w-full" wire:loading.attr="disabled">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />

        {{ __('filament::auth.resetPassword') }}
    </x-filament::button>
</x-filament::form>
