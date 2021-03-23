<div>
    <form
        wire:submit.prevent="attach"
        class="space-y-6"
    >
        <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

        <div class="space-y-3 sm:space-x-3 sm:space-y-0">
            <x-filament::button
                type="submit"
                color="primary"
                class="w-full sm:w-auto"
            >
                {{ __($attachButtonLabel) }}
            </x-filament::button>

            <x-filament::button
                type="button"
                color="primary"
                wire:click="attach(true)"
                class="w-full sm:w-auto"
            >
                {{ __($attachAnotherButtonLabel) }}
            </x-filament::button>

            <x-filament::button
                x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerAttachModal')"
                class="w-full sm:w-auto"
            >
                {{ __($cancelButtonLabel) }}
            </x-filament::button>
        </div>
    </form>
</div>
