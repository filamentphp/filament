@php
    $action = $getAction();
    $url = $getUrl();
@endphp

<x-filament::button
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$action"
    :href="$url"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :color="$getColor()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
>
    {{ $getLabel() }}
</x-filament::button>
