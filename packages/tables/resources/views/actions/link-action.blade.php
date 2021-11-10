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
