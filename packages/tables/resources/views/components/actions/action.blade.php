@props([
    'action',
    'component',
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
    :wire:click="$action->isEnabled() ? $wireClickAction : null"
    :href="$action->isEnabled() ? $action->getUrl() : null"
    :target="$action->shouldOpenUrlInNewTab() ? '_blank' : null"
    :disabled="$action->isDisabled()"
    :color="$action->getColor()"
    :tooltip="$action->getTooltip()"
    :icon="$action->getIcon()"
    :size="$action->getSize() ?? 'sm'"
>
    {{ $slot }}
</x-dynamic-component>
