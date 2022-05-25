@props([
    'action',
    'component',
])

@php
    $wireClickAction = $action->getAction() ? "mountFormComponentAction('{$action->getComponent()->getStatePath()}', '{$action->getName()}')" : null;
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('forms.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
    :tag="((! $action->getAction()) && $url) ? 'a' : 'button'"
    :wire:click="$action->isEnabled() ? $wireClickAction : null"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :tooltip="$action->getTooltip()"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :label="$action->getLabel()"
    :icon="$action->getIcon()"
>
    {{ $slot }}
</x-dynamic-component>
