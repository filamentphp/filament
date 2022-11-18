@props([
    'action',
    'component',
])

@php
    $isDisabled = $action->isDisabled();
    $url = $action->getUrl();
@endphp

<x-dynamic-component
    :component="$component"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes(), escape: false)"
    :tag="$url ? 'a' : 'button'"
    :wire:click="$action->getLivewireMountAction()"
    :href="$isDisabled ? null : $url"
    :target="($url && $action->shouldOpenUrlInNewTab()) ? '_blank' : null"
    :disabled="$isDisabled"
    :color="$action->getColor()"
    :tooltip="$action->getTooltip()"
    :label="$action->getLabel()"
    :icon="$action->getIcon()"
    :size="$action->getSize()"
    dusk="filament.forms.{{ $action->getComponent()->getStatePath() }}.action.{{ $action->getName() }}"
>
    {{ $slot }}
</x-dynamic-component>
