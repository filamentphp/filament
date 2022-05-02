@php
    $action = $getAction();

    $wireClickAction = $action ? "mountFormComponentAction('{$getComponent()->getStatePath()}', '{$getName()}')" : null;
@endphp

<x-forms::icon-button
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$isEnabled() ? $wireClickAction : null"
    :href="$isEnabled() ? $getUrl() : null"
    :tooltip="$getTooltip()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$isDisabled()"
    :color="$getColor()"
    :label="$getLabel()"
    :icon="$getIcon()"
    class="-my-2 filament-tables-icon-button-action"
/>
