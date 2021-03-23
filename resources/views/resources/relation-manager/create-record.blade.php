<div>
    <form
        wire:submit.prevent="create"
        class="space-y-6"
    >
        <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

        <div class="space-x-3">
            <x-filament::button
                type="submit"
                color="primary"
            >
                {{ __($createButtonLabel) }}
            </x-filament::button>

            <x-filament::button
                type="button"
                color="primary"
                wire:click="create(true)"
            >
                {{ __($createAnotherButtonLabel) }}
            </x-filament::button>

            <x-filament::button x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerCreateModal')">
                {{ __($cancelButtonLabel) }}
            </x-filament::button>
        </div>
    </form>
</div>
