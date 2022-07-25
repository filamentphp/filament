@props([
    'enabled' => false,
])

<x-tables::icon-button
    wire:click="toggleTableReordering"
    :icon="$enabled ? 'heroicon-o-check' : 'heroicon-o-selector'"
    :label="$enabled ? __('tables::table.buttons.disable_reordering.label') : __('tables::table.buttons.enable_reordering.label')"
    {{ $attributes->class(['filament-tables-reordering-trigger']) }}
/>
