@props([
    'records' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

<div {{ $attributes->merge(['class' => 'space-y-8']) }}>
    <div class="sm:flex items-center justify-end">
        <x-tables::filter :table="$table" />
    </div>

    <x-tables::table
        :records="$records"
        :sort-column="$sortColumn"
        :sort-direction="$sortDirection"
        :table="$table"
    />

    @if ($table->hasPagination())
        <x-tables::pagination.paginator :paginator="$records" />
    @endif
</div>
