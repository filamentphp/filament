@props([
    'action',
    'component',
    'icon' => null,
])

@php
    if ((! $action->getAction()) || $action->getUrl()) {
        $wireClickAction = null;
    } elseif (is_string($action->getAction())) {
        $wireClickAction = $action->getAction();
    } else {
        $wireClickAction = "mountAction('{$action->getName()}')";
    }
@endphp

<x-dynamic-component
    :component="$component"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes(), escape: true)"
    :form="$action->getFormToSubmit()"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :wire:click="$wireClickAction"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :type="$action->canSubmitForm() ? 'submit' : 'button'"
    :color="$action->getColor()"
    :key-bindings="$action->getKeybindings()"
    :tooltip="$action->getTooltip()"
    :disabled="$action->isDisabled()"
    :icon="$icon ?? $action->getIcon()"
    :size="$action->getSize()"
    :label-sr-only="$action->isLabelHidden()"
    dusk="filament.actions.action.{{ $action->getName() }}"
>
    {{ $slot }}
</x-dynamic-component>
