@props([
    'records' => [],
    'selected' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

<div {{ $attributes->merge(['class' => 'space-y-8']) }}>
    <div class="sm:flex items-center justify-between">
        <x-tables::delete-selected :selected="$selected" />

        <x-tables::filter :table="$table" />
    </div>

    <x-tables::table
        :records="$records"
        :selected="$selected"
        :sort-column="$sortColumn"
        :sort-direction="$sortDirection"
        :table="$table"
    />

    @if ($table->hasPagination())
        <x-tables::pagination.paginator :paginator="$records" />
    @endif
</div>
