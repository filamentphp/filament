@props([
    'action',
    'component',
    'icon' => null,
])

@php
    if ((! $action->getAction()) || $action->getUrl()) {
        $wireClickAction = null;
    } elseif ($record = $action->getRecord()) {
        $wireClickAction = "mountTableAction('{$action->getName()}', '{$this->getTableRecordKey($record)}')";
    } else {
        $wireClickAction = "mountTableAction('{$action->getName()}')";
    }
@endphp

<x-dynamic-component
    :component="$component"
    :dark-mode="config('tables.dark_mode')"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($action->getExtraAttributes())"
    :tag="$action->getUrl() ? 'a' : 'button'"
    :wire:click="$wireClickAction"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :tooltip="$action->getTooltip()"
    :icon="$icon ?? $action->getIcon()"
    :size="$action->getSize() ?? 'sm'"
    dusk="filament.tables.action.{{ $action->getName() }}"
>
    {{ $slot }}
</x-dynamic-component>
