<x-filament-support::button
    :color="$getColor()"
    :tag="$getUrl() ? 'a' : 'button'"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : false"
    :x-on:click="$shouldCloseNotification() ? 'close' : false"
    :wire:click="'$emit(\''.$getEvent().'\')'"
>
    {{ $getLabel }}
</x-filament-support::button>


