@props([
    'indicatorsCount' => null,
])

<x-tables::icon-button
    icon="heroicon-m-funnel"
    :label="__('tables::table.buttons.filter.label')"
    :indicator="$indicatorsCount"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
