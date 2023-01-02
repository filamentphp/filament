@props([
    'enabled' => false,
])

<x-filament::icon-button
    wire:click="toggleTableReordering"
    :icon="$enabled ? 'heroicon-o-check' : 'heroicon-o-chevron-up-down'"
    icon-alias="tables::reordering.trigger"
    :label="$enabled ? __('filament-tables::table.buttons.disable_reordering.label') : __('filament-tables::table.buttons.enable_reordering.label')"
    {{ $attributes->class(['filament-tables-reordering-trigger']) }}
/>
