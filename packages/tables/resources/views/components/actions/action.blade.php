@props([
    'action',
    'component',
])

@php
    if (! $action->getAction()) {
        $wireClickAction = null;
    } elseif ($record = $action->getRecord()) {
        $wireClickAction = "mountTableAction('{$action->getName()}', '{$record->getKey()}')";
    } else {
        $wireClickAction = "mountTableAction('{$action->getName()}')";
    }
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('tables.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :tag="((! $action->getAction()) && $action->getUrl()) ? 'a' : 'button'"
    :wire:click="$action->isEnabled() ? $wireClickAction : null"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :tooltip="$action->getTooltip()"
    :icon="$action->getIcon()"
    size="sm"
>
    {{ $slot }}
</x-dynamic-component>
