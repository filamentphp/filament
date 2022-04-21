<x-filament::page class="filament-resources-list-records-page">
    {{ $this->table }}

    @if (config('filament.shortcuts_enabled'))
        <div x-data
             x-init="
            Mousetrap.bindGlobal(['ctrl+o', 'command+o'], $event => {
                $event.preventDefault()

                document.getElementsByClassName('filament-header')[0].
                    getElementsByTagName('a')[0].click()
            })
        "
        ></div>
    @endif
</x-filament::page>
