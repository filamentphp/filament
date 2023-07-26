<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-range-summary text-sm'])
    }}
>
    @php
        $state = $formatState($getState());
        $from = $state[0] ?? null;
        $to = $state[1] ?? null;
    @endphp

    @if (filled($label = $getLabel()))
        <span class="text-gray-500 dark:text-gray-400">{{ $label }}:</span>
    @endif

    @if (filled($from))
        <span>
            {{ $from }}
        </span>
    @endif

    @if (filled($from) && filled($to))
        <span class="text-gray-500 dark:text-gray-400">-</span>
    @endif

    @if (filled($to))
        <span>
            {{ $to }}
        </span>
    @endif
</div>
