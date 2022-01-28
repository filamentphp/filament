@php
    $action = $getAction();
    $record = $getRecord();

    if (! $action) {
        $clickAction = null;
    } elseif ($record) {
        $clickAction = "mountTableAction('{$getName()}', '{$record->getKey()}')";
    } else {
        $clickAction = "mountTableAction('{$getName()}')";
    }
@endphp

<x-tables::icon-button
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$clickAction"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :color="$getColor()"
    :label="$getLabel()"
    :icon="$getIcon()"
    class="-my-2 filament-tables-icon-button-action"
/>
