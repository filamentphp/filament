@props([
    'indicatorsCount' => null,
])

<x-filament::icon-button
    icon="heroicon-m-funnel"
    color="gray"
    :label="__('filament-tables::table.buttons.filter.label')"
    :indicator="$indicatorsCount"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
