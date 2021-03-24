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

                    <div class="space-x-3">
                        <x-filament::button
                            type="submit"
                            color="primary"
                        >
                            {{ __(static::$createButtonLabel) }}
                        </x-filament::button>

                        <x-filament::button
                            type="button"
                            color="primary"
                            wire:click="create(true)"
                        >
                            {{ __(static::$createAnotherButtonLabel) }}
                        </x-filament::button>

                        <x-filament::button :href="$this->getResource()::generateUrl(static::$indexRoute)">
                            {{ __(static::$cancelButtonLabel) }}
                        </x-filament::button>
                    </div>
                </x-filament::card>
            @else
                <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

                <div class="space-x-3">
                    <x-filament::button
                        type="submit"
                        color="primary"
                    >
                        {{ __(static::$createButtonLabel) }}
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="primary"
                        wire:click="create(true)"
                    >
                        {{ __(static::$createAnotherButtonLabel) }}
                    </x-filament::button>

                    <x-filament::button :href="$this->getResource()::generateUrl(static::$indexRoute)">
                        {{ __(static::$cancelButtonLabel) }}
                    </x-filament::button>
                </div>
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
