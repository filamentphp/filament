<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-range-summary grid gap-y-1 px-3 py-4'])
    }}
>
    @php
        $state = $formatState($getState());
        $from = $state[0] ?? null;
        $to = $state[1] ?? null;
    @endphp

    @if (filled($label = $getLabel()))
        <span class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $label }}
        </span>
    @endif

    @if (filled($from) || filled($to))
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $from }}

            @if (filled($from) && filled($to))
                -
            @endif

            {{ $to }}
        </span>
    @endif
</div>
