<div>
    <x-filament::app-header
        :breadcrumbs="static::getBreadcrumbs()"
        :title="$title"
    />

    <x-filament::app-content>
        <x-filament::card>
            <form
                wire:submit.prevent="{{ $this->getForm()->getSubmitMethod() }}"
                class="space-y-6"
            >
                <x-forms::form :schema="$this->getForm()->getSchema()" :columns="$this->getForm()->getColumns()" />

                <x-filament::button
                    type="submit"
                    color="primary"
                >
                    {{ __(static::$createButtonLabel) }}
                </x-filament::button>
            </form>
        </x-filament::card>
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
