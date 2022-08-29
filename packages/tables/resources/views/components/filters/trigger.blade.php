@props(['getFilters'])
<x-tables::icon-button
    :icon="heroicon-o-filter"
    :indicator= "collect($getFilters)
    ->mapWithKeys(
        fn(\Filament\Tables\Filters\BaseFilter $filter): array => [$filter->getName() => $filter->getIndicators()],
    )
    ->filter(fn(array $indicators): bool => count($indicators))
    ->count()"
    x-on:click="$refs.popoverPanel.toggle"
    :label="__('tables::table.buttons.filter.label')"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
