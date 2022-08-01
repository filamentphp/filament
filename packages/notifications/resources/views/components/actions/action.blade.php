@props([
    'action',
    'component',
])

@php
    $wireClickAction = null;

    if ($action->getEvent()) {
        $emitArguments = collect([$action->getEvent()])
            ->merge($action->getEventData())
            ->map(fn (mixed $value) => \Illuminate\Support\Js::from($value)->toHtml())
            ->implode(', ');

        $wireClickAction = "\$emit($emitArguments)";
    }
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('notifications.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :wire:click="$action->isEnabled() ? $wireClickAction : null"
    :x-on:click="$action->isEnabled() && $action->shouldCloseNotification() ? 'close' : null"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :icon="$icon ?? $action->getIcon()"
    :size="$action->getSize() ?? 'sm'"
>
    {{ $slot }}
</x-dynamic-component>
