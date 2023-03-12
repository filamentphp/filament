@props([
    'action',
    'dynamicComponent',
    'icon' => null,
])

@php
    $isDisabled = $action->isDisabled();
    $url = $action->getUrl();
@endphp

<x-dynamic-component
    :component="$dynamicComponent"
    :form="$action instanceof \Filament\Actions\Contracts\SubmitsForm ? $action->getFormToSubmit() : null"
    :tag="$url ? 'a' : 'button'"
    :x-on:click="$action->getAlpineMountAction()"
    :wire:click="$action->getLivewireMountAction()"
    :href="$isDisabled ? null : $url"
    :target="($url && $action->shouldOpenUrlInNewTab()) ? '_blank' : null"
    :type="($action instanceof \Filament\Actions\Contracts\SubmitsForm && $action->canSubmitForm()) ? 'submit' : 'button'"
    :color="$action->getColor()"
    :key-bindings="$action->getKeyBindings()"
    :tooltip="$action->getTooltip()"
    :disabled="$isDisabled"
    :icon="$icon ?? $action->getIcon()"
    :indicator="$action->getIndicator()"
    :indicator-color="$action->getIndicatorColor()"
    :size="$action->getSize()"
    :label-sr-only="$action->isLabelHidden()"
    dusk="filament.actions.action.{{ $action->getName() }}"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes(), escape: false)"
>
    {{ $slot }}
</x-dynamic-component>
