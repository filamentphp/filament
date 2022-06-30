@props([
    'action',
    'component',
    'icon' => null,
])

@php
    if ((! $action->getAction()) || $action->getUrl()) {
        $wireClickAction = null;
    } elseif ($action->shouldOpenModal() || ($action->getAction() instanceof \Closure)) {
        $wireClickAction = "mountAction('{$action->getName()}')";
    } else {
        $wireClickAction = $action->getAction();
    }
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('filament.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
    :form="$action->getForm()"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :wire:click="$action->isEnabled() ? $wireClickAction : null"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :type="$action->canSubmitForm() ? 'submit' : 'button'"
    :color="$action->getColor()"
    :key-bindings="$action->getKeybindings()"
    :tooltip="$action->getTooltip()"
    :disabled="$action->isDisabled()"
    :icon="$icon ?? $action->getIcon()"
    :size="$action->getSize()"
>
    {{ $slot }}
</x-dynamic-component>
