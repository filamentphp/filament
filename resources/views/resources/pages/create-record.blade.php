<div>
    <x-filament::app-header
        :breadcrumbs="static::getBreadcrumbs()"
        :title="$title"
    />

    <x-filament::app-content>
        @php
            $schema = $this->getForm()->getSchema();
        @endphp

        <form wire:submit.prevent="create" class="space-y-6">
            @if ($this->getForm()->hasWrapper())
                <x-filament::card class="space-y-6">
                    <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

                    <x-filament::resources.forms.actions :actions="$this->getActions()" />
                </x-filament::card>
            @else
                <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

                <x-filament::resources.forms.actions :actions="$this->getActions()" />
            @endif
        </form>
    </x-filament::app-content>

    <div
        x-data
        x-init="
            Mousetrap.bindGlobal('mod+s', $event => {
                $event.preventDefault()

                document.querySelector(`button[type='submit']`).click()
            })
        "
    ></div>
</div>
