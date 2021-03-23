<div>
    <x-filament::app-header :title="$title" />

    <x-filament::app-content>
        <x-filament::card>
            <form
                wire:submit.prevent="save"
                class="space-y-6"
            >
                <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

                <x-filament::button
                    type="submit"
                    color="primary"
                >
                    {{ __('filament::edit-account.buttons.save.label') }}
                </x-filament::button>
            </form>
        </x-filament::card>
    </x-filament::app-content>
</div>
