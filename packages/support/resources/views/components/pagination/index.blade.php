@props([
    'currentPageOptionProperty' => 'tableRecordsPerPage',
    'extremeLinks' => false,
    'paginator',
    'pageOptions' => [],
])

@php
    use Illuminate\Pagination\LengthAwarePaginator;

    $isSimple = ! $paginator instanceof LengthAwarePaginator;
@endphp

<nav
    aria-label="{{ __('filament::components/pagination.label') }}"
    {{
        $attributes->class([
            'fi-pagination',
            'fi-simple' => $isSimple,
        ])
    }}
>
    @include('filament::components.pagination.previous-button')

    @include('filament::components.pagination.overview')

    @include('filament::components.pagination.records-per-page')

    @include('filament::components.pagination.next-button')

    @include('filament::components.pagination.items')
</nav>
