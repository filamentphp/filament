@php
    $action = $getAction();
    $record = $getRecord();

    $clickAction = null;

    if (is_string($action)) {
        if ($record) {
            $clickAction = "{$action}('{$record->getKey()}')";
        } else {
            $clickAction = $action;
        }
    } elseif ($action instanceof \Closure) {
        if ($record) {
            $clickAction = "mountTableAction('{$getName()}', '{$record->getKey()}')";
        } else {
            $clickAction = "mountTableAction('{$getName()}')";
        }
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
