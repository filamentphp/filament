<div>
    <x-filament::app-header :title="__($title)">
        <x-slot name="actions">
            <x-filament::modal>
                <x-slot name="trigger">
                    <x-filament::button
                        x-on:click="open = true"
                        color="danger"
                    >
                        Delete
                    </x-filament::button>
                </x-slot>

                <x-filament::card class="space-y-5 max-w-2xl">
                    <x-filament::card-header title="Delete this record?">
                        <p class="text-sm text-gray-500">
                            Are you sure you would like to delete this record? This action cannot be undone.
                        </p>
                    </x-filament::card-header>

                    <div class="space-y-3 sm:space-y-0 sm:flex sm:space-x-3 sm:justify-end">
                        <x-filament::button
                            x-on:click="open = false"
                        >
                            Cancel
                        </x-filament::button>

                        <x-filament::button
                            wire:click="delete"
                            color="danger"
                        >
                            Delete
                        </x-filament::button>
                    </div>
                </x-filament::card>
            </x-filament::modal>
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content>
        <x-filament::card>
            <x-forms::container :form="$this->getForm()" class="space-y-6">
                <x-filament::button
                    type="submit"
                    color="primary"
                >
                    Save
                </x-filament::button>
            </x-forms::container>
        </x-filament::card>
    </x-filament::app-content>
</div>
