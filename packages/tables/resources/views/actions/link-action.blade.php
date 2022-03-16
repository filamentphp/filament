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

<x-tables::link
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$clickAction"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$isDisabled()"
    :color="$getColor()"
    class="text-sm font-medium filament-tables-link-action"
>
    {{ $getLabel() }}
</x-tables::link>
