<div>
    <form
        wire:submit.prevent="{{ $this->getForm()->getSubmitMethod() }}"
        class="space-y-6"
    >
        <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

        <div class="space-x-3">
            <x-filament::button
                type="submit"
                color="primary"
            >
                {{ __($saveButtonLabel) }}
            </x-filament::button>

            <x-filament::button x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerEditModal')">
                {{ __($cancelButtonLabel) }}
            </x-filament::button>
        </div>
    </form>
</div>
