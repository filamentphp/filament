<x-tables::button
    :tag="((! $getAction()) && $getAction()) ? 'a' : 'button'"
    :wire:click="$getAction()"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :color="$getColor()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
>
    {{ $getLabel() }}
</x-tables::button>
