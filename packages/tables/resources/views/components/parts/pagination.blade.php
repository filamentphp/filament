@php
    use Illuminate\Pagination\LengthAwarePaginator;

    $hasDefaultItems = $part->hasDefaultItems();

    // A composed pagination shows itself as long as one of its parts renders something: the pagination parts hide themselves
    // without records, while a search field placed there stays.
    $itemsHtml = $hasDefaultItems ? null : $table->renderLayout($part->getChildSchema());
    $hasItems = $hasDefaultItems ? $table->hasPagination() : (! $table->isLayoutHtmlBlank($itemsHtml));
    $records = $table->isLoaded() ? $table->getRecords() : null;
@endphp

@if ($hasItems)
    {{-- Raw PHP instead of `@if`, so Livewire does not inject a block marker the original pagination did not have. --}}

    <?php if ($hasDefaultItems) { ?>

    <x-filament::pagination
        :extreme-links="$table->hasExtremePaginationLinks()"
        :page-options="$table->getPaginationPageOptions()"
        :paginator="$records"
    />

    <?php } else { ?>

    <nav
        aria-label="{{ __('filament::components/pagination.label') }}"
        @class([
            'fi-pagination fi-ta-pagination',
            'fi-simple' => $records && (! $records instanceof LengthAwarePaginator),
        ])
    >
        {!! $itemsHtml !!}
    </nav>

    <?php } ?>
@endif
