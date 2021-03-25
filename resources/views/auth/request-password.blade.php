<form
    wire:submit.prevent="submit"
    class="space-y-6"
>
    <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

    <x-filament::button type="submit" color="primary" class="w-full">
        {{ __('filament::auth/request-password.buttons.submit.label') }}
    </x-filament::button>
</form>
