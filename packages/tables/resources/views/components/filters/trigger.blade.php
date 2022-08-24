@props(['getFilters'])
<x-tables::icon-button
    :icon="count($getFilters) ? 'heroicon-s-filter' : 'heroicon-o-filter'"
    x-on:click="$refs.popoverPanel.toggle"
    :label="__('tables::table.buttons.filter.label')"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
