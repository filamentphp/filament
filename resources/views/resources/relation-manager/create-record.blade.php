<div>
    <form
        wire:submit.prevent="create"
        class="space-y-6"
    >
        <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

        <x-filament::resources.forms.actions :actions="$this->getActions()" />
    </form>
</div>
