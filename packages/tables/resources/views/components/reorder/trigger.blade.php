@props([
    'enabled' => false,
])

<x-filament-support::icon-button
    wire:click="toggleTableReordering"
    :icon="$enabled ? 'heroicon-o-check' : 'heroicon-o-chevron-up-down'"
    :label="$enabled ? __('filament-tables::table.buttons.disable_reordering.label') : __('filament-tables::table.buttons.enable_reordering.label')"
    {{ $attributes->class(['filament-tables-reordering-trigger']) }}
/>
