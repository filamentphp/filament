@if ($table->hasPagination())
    <x-filament::pagination
        :extreme-links="$table->hasExtremePaginationLinks()"
        :page-options="$table->getPaginationPageOptions()"
        :paginator="$table->getRecords()"
    />
@endif
