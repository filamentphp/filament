@props([
    'action',
    'component',
])

@php
    $wireClickAction = $action->getEvent() ? "\$emit('{$action->getEvent()}')" : null;
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('notifications.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :wire:click="$wireClickAction"
    :x-on:click="$action->shouldCloseNotification() ? 'close' : null"
    :href="$action->getUrl()"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :color="$action->getColor()"
>
    {{ $slot }}
</x-dynamic-component>
