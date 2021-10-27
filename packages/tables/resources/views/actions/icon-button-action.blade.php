@php
    $action = $getAction();
    $record = $getRecord();

    $clickAction = null;

    if (is_string($action)) {
        $clickAction = "{$action}('{$record->getKey()}')";
    } elseif ($action instanceof \Closure) {
        $clickAction = "mountTableAction('{$getName()}', '{$record->getKey()}')";
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
    class="-my-2"
/>
