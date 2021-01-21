<x-filament::form>
    <x-filament::card class="space-y-6">
        <x-filament::fields :fields="$this->fields()" />

        <x-filament::button
            type="submit"
            class="btn-primary"
            wire:loading.attr="disabled"
        >
            Update
        </x-filament::button>
    </x-filament::card>
</x-filament::form>
