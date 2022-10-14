@props([
    'footer' => null,
    'header' => null,
])

<table {{ $attributes->class([
    'filament-tables-table w-full text-left rtl:text-right divide-y table-auto',
    'dark:divide-gray-700' => config('tables.dark_mode'),
]) }}>
    @if ($header)
        <thead>
            <tr class="bg-gray-500/5">
                {{ $header }}
            </tr>
        </thead>
    @endif

    <tbody
        x-sortable
        x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
        @class([
            'divide-y whitespace-nowrap',
            'dark:divide-gray-700' => config('tables.dark_mode'),
        ])
    >
        {{ $slot }}
    </tbody>

    @if ($footer)
        <tfoot>
            <tr class="bg-gray-50">
                {{ $footer }}
            </tr>
        </tfoot>
    @endif
</table>
