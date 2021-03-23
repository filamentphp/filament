<div>
    <form
        wire:submit.prevent="save"
        class="space-y-6"
    >
        <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

        <div class="space-x-3">
            <x-filament::button
                type="submit"
                color="primary"
            >
                <x-filament::loader wire:target="save" class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />

                {{ __($saveButtonLabel) }}
            </x-filament::button>

            <x-filament::button x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerEditModal')">
                {{ __($cancelButtonLabel) }}
            </x-filament::button>
        </div>
    </form>
</div>
