<form
    wire:submit.prevent="{{ $this->getForm()->getSubmitMethod() }}"
    class="space-y-6"
>
    <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

    <x-filament::button type="submit" color="primary" class="w-full">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />

        {{ __('filament::auth.register') }}
    </x-filament::button>
</form>
