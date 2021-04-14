<div>
    <x-filament::app-header
        :breadcrumbs="static::getBreadcrumbs()"
        :title="$title"
    >
        <x-slot name="actions">
            @if ($this->canDelete())
                <x-filament::modal>
                    <x-slot name="trigger">
                        <x-filament::button
                            x-on:click="open = true"
                            color="danger"
                        >
                            {{ __(static::$deleteButtonLabel) }}
                        </x-filament::button>
                    </x-slot>

                    <x-filament::card class="max-w-2xl space-y-5">
                        <x-filament::card-header :title="static::$deleteModalHeading">
                            <p class="text-sm text-gray-500">
                                {{ __(static::$deleteModalDescription) }}
                            </p>
                        </x-filament::card-header>

                        <div class="space-y-3 sm:space-y-0 sm:flex sm:space-x-3 rtl:space-x-reverse sm:justify-end">
                            <x-filament::button
                                x-on:click="open = false"
                            >
                                {{ __(static::$deleteModalCancelButtonLabel) }}
                            </x-filament::button>

                            <x-filament::button
                                wire:click="delete"
                                color="danger"
                            >
                                {{ __(static::$deleteModalConfirmButtonLabel) }}
                            </x-filament::button>
                        </div>
                    </x-filament::card>
                </x-filament::modal>
            @endif
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content class="space-y-6">
        @php
            $schema = $this->getForm()->getSchema();
        @endphp

        <form wire:submit.prevent="save" class="space-y-6">
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

        <x-filament::resources.relations
            :owner="$record"
            :relations="static::getResource()::relations()"
        />
    </x-filament::app-content>

    <div
        x-data
        x-init="
            Mousetrap.bindGlobal(['ctrl+s', 'command+s'], $event => {
                $event.preventDefault()

                document.querySelector(`button[type='submit']`).click()
            })
        "
    ></div>
</div>
