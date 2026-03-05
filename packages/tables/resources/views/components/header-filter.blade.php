@props([
    'column',
])

@php
    $filter = $column->getHeaderFilter();
    $filterName = $filter->getName();
@endphp

<div
    {{ $attributes->class(['fi-ta-header-filter']) }}
>
    {{ $this->getTableHeaderFiltersForm()->getComponent($filterName)?->getChildSchema() }}
</div>
