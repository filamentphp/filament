@php
    $action = $getAction();
    $record = $getRecord();

    if (! $action) {
        $wireClickAction = null;
    } elseif ($record) {
        $wireClickAction = "mountTableAction('{$getName()}', '{$record->getKey()}')";
    } else {
        $wireClickAction = "mountTableAction('{$getName()}')";
    }
@endphp

<x-tables::link
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$isEnabled() ? $wireClickAction : null"
    :href="$isEnabled() ? $getUrl() : null"
    :tooltip="$getTooltip()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$isDisabled()"
    :color="$getColor()"
    :icon="$getIcon()"
    class="text-sm font-medium filament-tables-link-action"
>
    {{ $getLabel() }}
</x-tables::link>
