@props([
    'action',
    'component',
])

@php
    $wireClickAction = ($action->getAction() && (! $action->getUrl())) ?
        "mountFormComponentAction('{$action->getComponent()->getStatePath()}', '{$action->getName()}')" :
        null;
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('forms.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
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
    dusk="filament.forms.action.{{ $action->getName() }}"
>
    {{ $slot }}
</x-dynamic-component>
