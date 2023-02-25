@props([
    'action',
    'component',
    'icon' => null,
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
    :icon="$icon ?? $action->getIcon()"
    :size="$action->getSize()"
    dusk="filament.tables.action.{{ $action->getName() }}"
>
    {{ $slot }}
</x-dynamic-component>
