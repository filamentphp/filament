<div
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['filament-tables-range-summary px-4 py-3 text-sm']) }}
>
    @php
        $state = $formatState($getState());
        $from = $state[0] ?? null;
        $to = $state[1] ?? null;
    @endphp

    @if (filled($label = $getLabel()))
        <span class="text-gray-500 dark:text-gray-400">{{ $label }}:</span>
    @endif

    <span>
        {{ $from }}
    </span>

    @if (filled($from) && filled($to))
        <span class="text-gray-500 dark:text-gray-400">-</span>
    @endif

    <span>
        {{ $to }}
    </span>
</div>
