@props([
    'footer' => null,
    'header' => null,
    'reorderable' => false,
])

<table
    {{ $attributes->class(['filament-tables-table w-full table-auto divide-y text-start dark:divide-gray-700']) }}
>
    @if ($header)
        <thead>
            <tr class="bg-gray-500/5">
                {{ $header }}
            </tr>
        </thead>
    @endif

    <tbody
        @if ($reorderable)
            x-sortable
            x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
        @endif
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
