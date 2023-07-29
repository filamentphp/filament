@props([
    'footer' => null,
    'header' => null,
    'reorderable' => false,
])

<table
    {{ $attributes->class(['fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5']) }}
>
    @if ($header)
        <thead>
            <tr class="bg-gray-50 dark:bg-white/5">
                {{ $header }}
            </tr>
        </thead>
    @endif

    <tbody
        @if ($reorderable)
            x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
            x-sortable
        @endif
        class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5"
    >
        {{ $slot }}
    </tbody>

    @if ($footer)
        <tfoot>
            <tr class="bg-gray-50 dark:bg-white/5">
                {{ $footer }}
            </tr>
        </tfoot>
    @endif
</table>
