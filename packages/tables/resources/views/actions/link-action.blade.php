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

<x-tables::link
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$clickAction"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :color="$getColor()"
    class="text-sm font-medium"
>
    {{ $getLabel() }}
</x-tables::link>
