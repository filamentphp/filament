<x-filament::page class="filament-resources-list-records-page">
    {{ $this->table }}

    @if (config('filament.shortcuts.enabled'))
        <div x-data
             x-init="
            Mousetrap.bindGlobal(@js(config('filament.shortcuts.bindings.new')), $event => {
                $event.preventDefault()

                document.getElementsByClassName('filament-header')[0].
                    getElementsByTagName('a')[0].click()
            })
        "
        ></div>
    @endif
</x-filament::page>
