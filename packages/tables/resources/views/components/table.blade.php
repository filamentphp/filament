@props([
    'footer' => null,
    'header' => null,
])

<table {{ $attributes->class(['filament-tables-table w-full text-start divide-y table-auto dark:divide-gray-700']) }}>
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
        class="divide-y whitespace-nowrap dark:divide-gray-700"
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
