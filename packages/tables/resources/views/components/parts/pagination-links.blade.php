@php
    use Filament\Tables\Components\TablePagination;

    $isStandalone = ! $part->getContainer()->getParentComponent() instanceof TablePagination;
    $paginator = $table->getRecords();
@endphp

@if ($table->hasPagination())
    <div
        @class([
            'fi-pagination-links',
            'fi-pagination-part' => $isStandalone,
        ])
    >
        @include('filament::components.pagination.previous-button')

        @include('filament::components.pagination.items', ['extremeLinks' => $table->hasExtremePaginationLinks()])

        @include('filament::components.pagination.next-button')
    </div>
@endif
