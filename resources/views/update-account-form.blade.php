<x-filament::card>
    <x-filament::form :fields="$this->fields()" class="space-y-6">
        <x-filament::button
            type="submit"
            class="btn-primary"
            wire:loading.attr="disabled"
        >
            {{ __('filament::update-account-form.update') }}
        </x-filament::button>
    </x-filament::form>
</x-filament::card>
