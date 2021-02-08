<x-slot name="actions">
    <x-filament::modal>
        <x-slot name="trigger">
            <x-filament::button
                x-on:click="open = true"
                class="btn-danger"
                wire:loading.attr="disabled"
            >
                Delete
            </x-filament::button>
        </x-slot>

        <x-filament::card class="space-y-5 max-w-2xl">
            <div class="space-y-2">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                    Delete this record?
                </h3>

                <p class="text-sm text-gray-500">
                    Are you sure you would like to delete this record? This action cannot be undone.
                </p>
            </div>

            <div class="space-y-3 sm:space-y-0 sm:flex sm:space-x-3 sm:justify-end">
                <x-filament::button
                    x-on:click="open = false"
                    class="btn"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-filament::button>

                <x-filament::button
                    wire:click="delete"
                    class="btn-danger"
                    wire:loading.attr="disabled"
                >
                    Delete
                </x-filament::button>
            </div>
        </x-filament::card>
    </x-filament::modal>
</x-slot>

<x-filament::card>
    <x-filament::form :fields="$this->getForm()->fields" class="space-y-6">
        <x-filament::button
            type="submit"
            class="btn-primary"
            wire:loading.attr="disabled"
        >
            Update
        </x-filament::button>
    </x-filament::form>
</x-filament::card>
