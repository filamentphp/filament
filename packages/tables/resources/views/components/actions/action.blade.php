@props([
    'action',
    'component',
    'icon' => null,
])

@php
    if ((! $action->getAction()) || $action->getUrl()) {
        $wireClickAction = null;
    } elseif ($record = $action->getRecord()) {
        $wireClickAction = "wireClickAction('{$action->getName()}', '{$this->getTableRecordKey($record)}')";
    } else {
        $wireClickAction = "wireClickAction('{$action->getName()}')";
    }
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('tables.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :tooltip="$action->getTooltip()"
    :icon="$icon ?? $action->getIcon()"
    :size="$action->getSize() ?? 'sm'"
    x-on:click="{!! $action->isEnabled() ? $wireClickAction : null !!}"
>
    {{ $slot }}
</x-dynamic-component>
