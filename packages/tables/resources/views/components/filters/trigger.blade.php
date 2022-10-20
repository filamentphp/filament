@props([
    'indicatorsCount' => null,
])

<x-filament-tables::icon-button
    icon="heroicon-o-funnel"
    :label="__('filament-tables::table.buttons.filter.label')"
    :indicator="$indicatorsCount"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
