@props([
    'footer' => null,
    'header' => null,
    'headerGroups' => null,
    'reorderable' => false,
    'reorderAnimationDuration' => 300,
])

<table
    {{ $attributes->class(['fi-ta-table']) }}
>
    @if ($header)
        <thead>
            @if ($headerGroups)
                <tr class="fi-ta-table-head-groups-row">
                    {{ $headerGroups }}
                </tr>
            @endif

            <tr>
                {{ $header }}
            </tr>
        </thead>
    @endif

    <tbody
        @if ($reorderable)
            x-on:end.stop="
                $wire.reorderTable(
                    $event.target.sortable.toArray(),
                    $event.item.getAttribute('x-sortable-item'),
                )
            "
            x-sortable
            data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
        @endif
    >
        {{ $slot }}
    </tbody>

    @if ($footer)
        <tfoot>
            <tr>
                {{ $footer }}
            </tr>
        </tfoot>
    @endif
</table>
