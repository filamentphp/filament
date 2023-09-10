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
    :badge="$action->getBadge()"
    :badge-color="$action->getBadgeColor()"
    :component="$dynamicComponent"
    :form="$action->getFormToSubmit()"
    :tag="$url ? 'a' : 'button'"
    :x-on:click="$action->getAlpineClickHandler()"
    :wire:click="$action->getLivewireClickHandler()"
    :wire:target="$action->getLivewireTarget()"
    :href="$isDisabled ? null : $url"
    :target="($url && $action->shouldOpenUrlInNewTab()) ? '_blank' : null"
    :type="$action->canSubmitForm() ? 'submit' : 'button'"
    :color="$action->getColor()"
    :key-bindings="$action->getKeyBindings()"
    :tooltip="$action->getTooltip()"
    :disabled="$isDisabled"
    :icon="$icon ?? $action->getIcon()"
    :icon-size="$action->getIconSize()"
    :size="$action->getSize()"
    :label-sr-only="$action->isLabelHidden()"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes(), escape: false)"
>
    {{ $slot }}
</x-dynamic-component>
