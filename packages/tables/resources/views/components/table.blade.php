@props([
    'footer' => null,
    'header' => null,
])

<table
    {{
        $attributes->class([
            'filament-tables-table w-full table-auto divide-y text-start',
            'dark:divide-gray-700' => config('tables.dark_mode'),
        ])
    }}
>
    @if ($header)
        <thead>
            <tr class="bg-gray-500/5">
                {{ $header }}
            </tr>
        </thead>
    @endif

    <tbody
        wire:sortable
        wire:end.stop="reorderTable($event.target.sortable.toArray())"
        wire:sortable.options="{ animation: 100 }"
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
