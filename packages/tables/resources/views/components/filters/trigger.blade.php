@props([
    'indicatorsCount' => null,
])

<x-filament::icon-button
    icon="heroicon-m-funnel"
    icon-alias="tables::filters.trigger"
    color="gray"
    :label="__('filament-tables::table.buttons.filter.label')"
    :indicator="$indicatorsCount"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
