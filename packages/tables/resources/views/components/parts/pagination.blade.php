@php
    use Illuminate\Pagination\LengthAwarePaginator;
@endphp

@if ($table->hasPagination())
    {{-- Raw PHP instead of `@if`, so Livewire does not inject a block marker the original pagination did not have. --}}

    <?php if ($part->hasDefaultItems()) { ?>

    <x-filament::pagination
        :extreme-links="$table->hasExtremePaginationLinks()"
        :page-options="$table->getPaginationPageOptions()"
        :paginator="$table->getRecords()"
    />

    <?php } else { ?>

    <nav
        aria-label="{{ __('filament::components/pagination.label') }}"
        @class([
            'fi-pagination fi-ta-pagination',
            'fi-simple' => ! $table->getRecords() instanceof LengthAwarePaginator,
        ])
    >
        {!! $table->renderLayout($part->getChildSchema()) !!}
    </nav>

    <?php } ?>
@endif
