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
    :color="$action->getColor()"
    :component="$dynamicComponent"
    :disabled="$isDisabled"
    :form="$action->getFormToSubmit()"
    :href="$isDisabled ? null : $url"
    :icon="$icon ?? $action->getIcon()"
    :icon-size="$action->getIconSize()"
    :key-bindings="$action->getKeyBindings()"
    :label-sr-only="$action->isLabelHidden()"
    :tag="$url ? 'a' : 'button'"
    :target="($url && $action->shouldOpenUrlInNewTab()) ? '_blank' : null"
    :tooltip="$action->getTooltip()"
    :type="$action->canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$action->getLivewireClickHandler()"
    :wire:target="$action->getLivewireTarget()"
    :x-on:click="$action->getAlpineClickHandler()"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes(), escape: false)"
>
    {{ $slot }}
</x-dynamic-component>
