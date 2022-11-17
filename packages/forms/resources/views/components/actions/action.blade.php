@props([
    'action',
    'component',
])

@php
    if (! $action->getUrl()) {
        $wireClickAction = "mountFormComponentAction('{$action->getComponent()->getStatePath()}', '{$action->getName()}')";
    } else {
        $wireClickAction = null;
    }
@endphp

<x-dynamic-component
    :component="$component"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes(), escape: false)"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :wire:click="$wireClickAction"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :tooltip="$action->getTooltip()"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :label="$action->getLabel()"
    :icon="$action->getIcon()"
    :size="$action->getSize()"
    dusk="filament.forms.{{ $action->getComponent()->getStatePath() }}.action.{{ $action->getName() }}"
>
    {{ $slot }}
</x-dynamic-component>
