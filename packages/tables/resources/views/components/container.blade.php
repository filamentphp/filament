@props([
    'records' => [],
    'selected' => [],
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'table',
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <div class="items-center justify-between space-y-4 sm:flex sm:space-y-0">
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
