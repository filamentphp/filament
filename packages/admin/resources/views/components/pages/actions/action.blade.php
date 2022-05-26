@props([
    'action',
    'component',
])

@php
    if (! $action->getAction()) {
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
    :tag="((! $action->getAction()) && $action->getUrl()) ? 'a' : 'button'"
    :wire:click="$action->isEnabled() ? $wireClickAction : null"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :type="$action->canSubmitForm() ? 'submit' : 'button'"
    :color="$action->getColor()"
    :key-bindings="$action->getKeybindings()"
    :tooltip="$action->getTooltip()"
    :disabled="$action->isDisabled()"
    :icon="$action->getIcon()"
>
    {{ $slot }}
</x-dynamic-component>
