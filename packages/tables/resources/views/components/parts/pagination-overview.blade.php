@php
    use Filament\Tables\Components\TablePagination;

    $isStandalone = ! $part->getContainer()->getParentComponent() instanceof TablePagination;
@endphp

@if ($table->hasPagination())
    {{-- Raw PHP instead of `@if`, so Livewire's block markers do not split the wrapper's tags. --}}

    <?php if ($isStandalone) { ?>
    <div class="fi-pagination-part">
        <?php } ?>

        @include('filament::components.pagination.overview', ['paginator' => $table->getRecords()])

        <?php if ($isStandalone) { ?>
    </div>
    <?php } ?>
@endif
