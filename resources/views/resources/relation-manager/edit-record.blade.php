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
                {{ __($this->manager::$editModalSaveButtonLabel) }}
            </x-filament::button>

            <x-filament::button x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerEditModal')">
                {{ __($this->manager::$editModalCancelButtonLabel) }}
            </x-filament::button>
        </div>
    </form>
</div>
