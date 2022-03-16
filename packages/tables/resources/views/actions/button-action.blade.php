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

    $clickAction = $isDisabled() ? null : $clickAction;
@endphp

<x-tables::button
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$clickAction"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$isDisabled()"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    size="sm"
    class="filament-tables-button-action"
>
    {{ $getLabel() }}
</x-tables::button>
